<?php
/**
* This will used to perform all shipping operation with clickpost for any particular order 
*
* @author     Radixweb
* @copyright  Copyright (c) 2021, Radixweb
* @version    1.0
* @since      1.0
*/

class ClickPost {
	// constants
	
	public function createOrder($cp_id, $orderData){
        $orderApiData = $this->prepareOrderData($cp_id, $orderData);
		if (isset($orderApiData) && !empty($orderApiData)) {
			$orderApiData = json_encode($orderApiData);
			$url = self::ORDER_CREATE_API_URL;
			$responseData = $this->curlFunctionCall('POST', $orderApiData, $url);
			$responseSaveOrder =  $this->saveOrderShippingDetails($responseData);
			if($responseSaveOrder == 1) {
				$this->clickpost_copy_log('      API                                === '.$url);
				$this->clickpost_copy_log('      SUCCESS                            === '.date("Y-m-d H:i:s"));
			}
			return $responseSaveOrder;
			
		} else {
			$this->clickpost_copy_log('      createOrder >> prepareOrderData Order detail not found ===  Date : '.date("Y-m-d H:i:s"));
			return 0;
		}
	}
	
	/**
    * prepareOrderData() is Used to prepare orderdata that we need to send to clickpost via api
    *
    * @access public
	* @param int $cp_id required
    * @return Array
    */
	public function prepareOrderData($cp_id, $orderData){
		$order = $pickup_info = $drop_info = $shipment_details = $additional = [];
		if (isset($orderData) && !empty($orderData)) {
			$weight = ($orderData[0]->total_weight > 0) ? (int)($orderData[0]->total_weight * 1000 ) : 1000 ;
			$this->invoice_value = (float)$orderData[0]->price;			
			// static values
			$pickup_info['pickup_state']	= $this->pickup_state;
			$pickup_info['pickup_address']	= $this->pickup_address;
			$pickup_info['email']			= $this->pickup_email;
			$pickup_info['pickup_time']		= $this->pickup_time; // Niks need to check or Ask 
			$pickup_info['pickup_pincode']	= $this->pickup_pincode;
			$pickup_info['pickup_city'] 	= $this->pickup_city;
			$pickup_info['pickup_name']		= $this->pickup_name;
			$pickup_info['pickup_country'] 	= $this->pickup_country;
			$pickup_info['pickup_phone']	= $this->pickup_phone; 
			$pickup_info['order_type']		= $this->order_type;
			// delivery information 
			$drop_address = $orderData[0]->delivery_street_address;
			if(!empty($orderData[0]->delivery_company))
				$drop_address = $orderData[0]->delivery_company.', '.$drop_address;
			if(!empty($orderData[0]->delivery_suburb))
				$drop_address = $drop_address.', '.$orderData[0]->delivery_suburb;
				
			$drop_info['drop_address']		= $drop_address;
			$drop_info['drop_phone']		= $orderData[0]->delivery_telephone;
			$drop_info['drop_country']		= $orderData[0]->delivery_country;
			$drop_info['drop_state']		= $orderData[0]->delivery_state;
			$drop_info['drop_pincode']		= $orderData[0]->delivery_postcode; 
			$drop_info['drop_city']			= $orderData[0]->delivery_city;
			$drop_info['drop_name']			= $orderData[0]->delivery_name;
			$drop_info['drop_email']		= $orderData[0]->customers_email_address;
			// Shipment order details
			$shipment_details['height']				= $this->heigh;
			$shipment_details['order_type']			= $this->order_type;
			$shipment_details['invoice_value']		= $this->invoice_value;
			$shipment_details['invoice_number'] 	= $this->orderId ;
			$shipment_details['invoice_date']		= date('Y-m-d', strtotime($orderData[0]->orders_date_finished));
			$shipment_details['reference_number']	= $this->reference_number;
			$shipment_details['length'] 			= $this->lenght;
			$shipment_details['breadth'] 			= $this->breadth;
			$shipment_details['weight'] 			= $weight; 
			$shipment_details['items'] 				= [["price"=>$this->invoice_value,"description"=>"PID : ".$orderData[0]->products,"sku"=>$orderData[0]->order_sku,"quantity"=>$orderData[0]->quantity,"weight"=>$weight]]; // Json list with multiple item objects in it 
			$shipment_details['cod_value'] 			= $this->cod_value; 
			$shipment_details['courier_partner'] 	= $cp_id; 
			// Additional information 
			$additional['label']			= true;
			$additional['delivery_type']	= $this->delivery_type;
			$additional['async']			= false;			
			$additional['account_code']		= $this->shippingMode;
			$additional['order_date']		= date("Y-m-d");
			$additional['order_id']			= (string)$this->orderId;
			$additional['return_info']['pincode']	= $this->return_pincode;
			$additional['return_info']['address']	= $this->return_address;
			$additional['return_info']['state']		= $this->return_state;
			$additional['return_info']['phone']		= $this->return_phone; 
			$additional['return_info']['name']		= $this->return_name; 
			$additional['return_info']['city']		= $this->return_city;
			$additional['return_info']['country']	= $this->return_country;
			
			$error = 0;
			if (empty($shipment_details['courier_partner']) || empty($additional['account_code'])) {
				$error = $error+1;
				$this->clickpost_copy_log('         API parameter/Value missing/blank Please check order data    === '.date("Y-m-d H:i:s"));
			} 
			if (isset($drop_info['drop_pincode']) && !empty($drop_info['drop_pincode']) && $drop_info['drop_pincode'] == '000000') {
				// this condition is based on Task specification (196929) Case 1
				$this->clickpost_copy_log('         drop_pincode value should not be 000000    === '.date("Y-m-d H:i:s"));
				$error = $error+1;
			}
			/* if(empty($orderData[0]->price) || $orderData[0]->price == '0') {
				// this condition is based on Task specification (210663) Case 5
				$this->clickpost_copy_log('         Order amount is zero (0)    === '.date("Y-m-d H:i:s"));
				$error = $error+1;
			} */
			
			$order['pickup_info']		= $pickup_info;
			$order['drop_info'] 		= $drop_info;
			$order['additional'] 		= $shipment_details;
			$order['shipment_details'] 	= $additional;
			
			// logic for case 2
			$fixShippingAddress = 0;
			if (isset($orderData[0]->corporate_settings) && $orderData[0]->corporate_settings =! '' ) {
				$corsettings = json_decode($orderData[0]->corporate_settings,true);
				if($corsettings['corporate_setting_shipping_options'] == 'fix_shipping_options'){
					$fixShippingAddress = 1;
				}
			}
			// logic for case 3 managed using $this->allow_clickpost param 
			//logic for case 4
			$orderProducts = explode(" ",$orderData[0]->products);
			$productSkipClickpost = 0;
			foreach ($orderProducts as $key => $product) {
				$productData = getProductSetting( $field = "", $product, PRODUCT_IS_ALLOW_CLICKPOST, "var" );
				if(isset($productData['PRODUCT_IS_ALLOW_CLICKPOST']) && $productData['PRODUCT_IS_ALLOW_CLICKPOST'] == 0) {
					$productSkipClickpost = $productSkipClickpost+1;
				}
			}
			// Logic of case 5 Task #206332 Gifting product :: PRINTSTOP skip when gifting product with multiple location 
			$IsShipToMultipleLocation = false;
			$IsShipToMultipleLocation = $this->getShipToMultipleLocation($orderData[0]->order_products_ids);		
			// special condition for task (196929) case 2 case 3 case 4 and case 5 for task (206332)
			if ($error > 0  || ($this->allow_clickpost == 0 && $this->allow_clickpost != '') || $fixShippingAddress == 1 || $productSkipClickpost != 0 || isOrderItemWise($this->orderId) || $IsShipToMultipleLocation) {
				if ($fixShippingAddress == 1) {
					$logMessage = '         Shipping type is fixed so this request could not be pass to clickpost. === '.date('Y-m-d H:i:s');
				} else if($this->allow_clickpost == 0){
					$logMessage = '         Shipping method is not allwing clickpost right now Please enable this option. === '.date('Y-m-d H:i:s');
				} else if ($productSkipClickpost != 0) {	
					$logMessage = '         Any of the order product is not allowing the clickpost so this order is skipped. === '.date('Y-m-d H:i:s');
				} else if (isOrderItemWise($this->orderId)) {
					$logMessage = '         Restrict clickpost for  Item Level shipment . === '.date('Y-m-d H:i:s');
				} else {
					$logMessage = '         API parameter/Value missing/blank Please check order data === '.date('Y-m-d H:i:s');
				}
				$this->clickpost_copy_log($logMessage);
				$order = [];
			}
			
		} else {
			$logMessage = '         Order data not found for OrderId->'.$this->orderId;
			$this->clickpost_copy_log($logMessage);
		}
		return $order;
	}
	/**
    * curlFunctionCall() is Used to call each and every curl call of clickpost 
    *
    * @access public
	* @param string $method required POST,GET,ETC
	* @param string $data required data should be pass as api needed
	* @param int $url required api url
    * @return Array
    */
	public function curlFunctionCall($method,$data = [],$url){
		$response = [];
		$curl = curl_init();
		$curlArray[CURLOPT_URL] = $url;
		$curlArray[CURLOPT_RETURNTRANSFER] = $url;
		$curlArray[CURLOPT_CUSTOMREQUEST] = $method;
		if ($method == 'POST') {
			$curlArray[CURLOPT_POSTFIELDS] = $data;
			$curlArray[CURLOPT_HTTPHEADER] = array("Content-Type: application/json");
		}
		curl_setopt_array($curl, $curlArray);
		$result = curl_exec($curl);
		$this->clickpost_copy_log($url);
		$this->clickpost_copy_log($method);
		$this->clickpost_copy_log($data);
		if (curl_errno($curl)) {
			$error_msg = curl_error($curl);
			$this->clickpost_copy_log($error_msg);
			$this->clickpost_copy_log($url);
			$this->clickpost_copy_log($method);
			$this->clickpost_copy_log($data);
			return [];
		}
		curl_close($curl);
		$response = json_decode($result, true);
		if (!isset($response) || empty($response)){
			$this->clickpost_copy_log('      createOrder >> prepareOrderData >> curlFunctionCall Blank response from curl -> Date : '.date("Y-m-d H:i:s"));
			$this->clickpost_copy_log($url);
			$this->clickpost_copy_log($method);
			$this->clickpost_copy_log($data);
			$this->clickpost_copy_log($result);
			return [];
		}
		return $response;
	}
	/**
    * saveOrderShippingDetails() is Used to save orderShipping details like courier,waybill etc in our local database 
    *
    * @access public
	* @param string $responseData required 
    * @return Array
    */
	public function saveOrderShippingDetails($responseData) {
		$OrderMasterObj = new OrderMaster();
		$OrderDataObj = new OrderData();
		if (isset($responseData) && !empty($responseData) && isset($responseData['result']) && !empty($responseData['result'])) {
			$OrderDataObj->airway_bill_number = $responseData['result']['waybill'];
			$OrderDataObj->cp_id = $responseData['result']['courier_partner_id'];
			$OrderDataObj->courirer_company_name = $responseData['result']['courier_name'];
			//$OrderDataObj->shipping_mode = $this->shippingMode;
			$OrderDataObj->orders_id = $this->orderId;
			$OrderMasterObj->editOrder($OrderDataObj);
			return 1;
		}else {
			$this->clickpost_copy_log('         createOrder >> prepareOrderData >> saveOrderShippingDetails Curl response not proper  Date : '.date("Y-m-d H:i:s"));
			$this->clickpost_copy_log($responseData);
			return 0;
		}
	}
	/**
    * getShippinglabel() is Used to get label url from clickpost 
    *
    * @access public
	* @param string $waybill required 
    * @return Array
    */
	public function getShippinglabel($waybill) { 
		if (isset($waybill) && !empty($waybill)) {
			$url = self::GET_SHIPPING_LABEL_URL.'&waybill='.$waybill;
			$responseData = $this->curlFunctionCall('GET', [], $url);
			if(isset($responseData) && !empty($responseData) && isset($responseData['meta']['success']) && $responseData['meta']['success'] == 1){
				$response['success'] = true;
				$response['message'] = '';
				$response['label'] = $responseData['result']['shipping_label'];
			} else {
				$response['success'] = false;
				$response['message'] = '';
				$response['label'] = '';
			}
			
		} else {
			$this->clickpost_copy_log('   Waybill number missing     ==='.$waybill.'  Date : '.date("Y-m-d H:i:s"));
			$response['success'] = false;
			$response['message'] = '';
			$response['label'] = '';
		}
		return $response;
	}
	/**
    * trackOrder() is Used to get all the tracking information from clickpost 
    *
    * @access public
	* @param string $waybill required 
	* @param string $co_id required 
    * @return Array
    */
	public function trackOrder($waybill, $cp_id) { 
		if (isset($waybill) && !empty($waybill) && isset($cp_id) && !empty($cp_id)) {	
			$url = self::TRACK_ORDER_URL.'&waybill='.$waybill.'&cp_id='.$cp_id; 
			$responseData = $this->curlFunctionCall('GET', [], $url);
			if(isset($responseData) && !empty($responseData) && isset($responseData['meta']['success']) && $responseData['meta']['success'] == '1'){
				$response['success'] = true;
				$response['message'] = [];
				$response['trackingInfo'] = $responseData['result'][$waybill]['latest_status'];
			} else {
				$response['success'] = false;
				$response['message'] = '';
				$response['trackingInfo'] = [];
			}
		} else {
			$this->clickpost_copy_log('   Waybill/Courier-partner number is missing     ===  Date : '.date("Y-m-d H:i:s"));
			$response['success'] = false;
			$response['message'] = '';
			$response['trackingInfo'] = [];
		}
		return json_encode($response);
	}
	// Generate order Block
	// This function will create a order data folder and store intoimage folder
	public function generateOrderFolder($orderData){
		if(isset($orderData) && !empty($orderData)) {
			$this->clickpost_copy_log('   generateOrderFolder Start  === '.date("Y-m-d H:i:s"));
			$path = self::GENERATE_ORDER_PATH;
			foreach ($path as $key => $value) {
				if (! is_dir( $value ))
					mkdir( $value, DEFAULT_FILE_PERMS, true );
			}
			$OrdersData['UserType'] = $orderData[0]['user_type_id'];
			$OrdersData['Path'] = $path[0];
			$OrdersData['OrderID'] = $orderData[0]['orders_id'];
			$OrdersData['CorporateInvoice']  = $orderData[0]['corporate_invoice'];
	
			if(!empty($OrdersData)) {    
				$OrderDetails = array(); 
				if($OrdersData['UserType'] == '2') {
					$OrderDetails['corporate'][] = $OrdersData;
				} elseif($OrdersData['UserType'] == '3') {
					$OrderDetails['reseller'][] = $OrdersData;
				} else {
					$OrderDetails['retailer'][] = $OrdersData;
				}
				$this->hot_folder_exta_info_clickpost($OrderDetails);
			}
			$this->clickpost_copy_log('   generateOrderFolder End    === '.date("Y-m-d H:i:s"));
		}
	}
	/**
	 * Function to create Invoice, Job Ticket, Label, QR code, XML for all orders in single file
	 *
	 * @param array $OrdersData
	 * @return boolean
	 */
	public function hot_folder_exta_info_clickpost($OrdersData) {
		$OrderDetails = $OrdersData['corporate']; 
		// Corporate Copy :: Start
		if(isset($OrdersData['corporate']) && !empty($OrdersData['corporate'])) {
			$invoice_template = 'GST_CORPORATE_INVOICE_TEMPLATE';
			$OrderDetails  = $OrdersData['corporate'];
			if($OrderDetails[0]['CorporateInvoice'] == '1') { $invoice_template = 'GST_INVOICE_TEMPLATE'; }
			$OrderDetails[0]['Path'] = $OrderDetails[0]['Path'].'/invoice/';
			$this->generate_pdf_xml_data_clickpost('INVOICE', $OrderDetails, $invoice_template);
			$OrderDetails[0]['Path'] = str_replace('invoice', 'job_ticket', $OrderDetails[0]['Path']);
			$this->generate_pdf_xml_data_clickpost('JOB_TICKET', $OrderDetails);
			$OrderDetails[0]['Path'] = str_replace('job_ticket', 'label', $OrderDetails[0]['Path']);
			$this->generate_pdf_xml_data_clickpost('LABEL', $OrderDetails);
		}
		// Corporate Copy :: End
		
		// Retailer Copy :: Start
		if(isset($OrdersData['retailer']) && !empty($OrdersData['retailer'])) {
			$OrderDetails  = $OrdersData['retailer'];
			$OrderDetails[0]['Path'] = $OrderDetails[0]['Path'].'/invoice/';
			$this->generate_pdf_xml_data_clickpost('INVOICE', $OrderDetails, 'GST_INVOICE_TEMPLATE');
			$OrderDetails[0]['Path'] = str_replace('invoice', 'job_ticket', $OrderDetails[0]['Path']);
			$this->generate_pdf_xml_data_clickpost('JOB_TICKET', $OrderDetails);
			$OrderDetails[0]['Path'] = str_replace('job_ticket', 'label', $OrderDetails[0]['Path']);
			$this->generate_pdf_xml_data_clickpost('LABEL', $OrderDetails);     
		}
		// Retailer Copy :: End
		
		// Reseller Copy :: Start
		if(isset($OrdersData['reseller']) && !empty($OrdersData['reseller'])) {
			$OrderDetails  = $OrdersData['reseller'];
			$OrderDetails[0]['Path'] = $OrderDetails[0]['Path'].'/invoice/';
			$this->generate_pdf_xml_data_clickpost('INVOICE', $OrderDetails, 'GST_INVOICE_TEMPLATE');
			$OrderDetails[0]['Path'] = str_replace('invoice', 'job_ticket', $OrderDetails[0]['Path']);
			$this->generate_pdf_xml_data_clickpost('JOB_TICKET', $OrderDetails);
			$OrderDetails[0]['Path'] = str_replace('job_ticket', 'label', $OrderDetails[0]['Path']);
			$this->generate_pdf_xml_data_clickpost('LABEL', $OrderDetails);   
		}
		// Reseller Copy :: End    
	}
	public function generate_pdf_xml_data_clickpost($action, $OrdersData, $user_invoice_template = '', $label_path = '', $number = '') {
		require_once(DIR_WS_DOMPDF . "dompdf_config.inc.php");
		global $dompdf, $log_message;
		$orderId = $OrdersData[0]['OrderID'];
		foreach ($OrdersData as  $OData) {
			$OrderId = $OData['OrderID'];
			$path    = $OData['Path'];
	
			switch ($action) {
				CASE 'INVOICE':
					$invoice_data .= getCommonMailVariables($OrderId, $user_invoice_template, null, true);
					break;
				CASE 'LABEL':
					if(isOrderItemWise($OrderId)) {
						$label_data = getProductInvoiceData($OrderId, null, 'LABEL_TEMPLATE', null, true).$break.'<br>';
					} else {
						$label_data = getCommonMailVariables($OrderId, 'LABEL_TEMPLATE', null, true);
					}
					break;
				CASE 'JOB_TICKET':
					$job_ticket_data .= getCommonMailVariables($OrderId, 'PRINT_JOB_ORDER', null, true).$break;
					break;
			}
		}
		switch ($action) {
			CASE 'INVOICE':
				if(!empty($invoice_data)) {
					$Invoice_Filename = $orderId.'.pdf';
					try{
						$invoice_data = str_replace(SITE_URL, SITE_DOCUMENT_ROOT, $invoice_data);
	
						$dompdf = new DOMPDF();
						$dompdf->load_html($invoice_data);
						$dompdf->set_paper('a4', 'portrait');
						$dompdf->render();
	
						$this->write_local($path, $Invoice_Filename);
						$this->clickpost_copy_log('Generate Invoice File '.$Invoice_Filename);
					} catch(Exception $e) {
						$this->clickpost_copy_log('Can not generate Invoice File');
					}
					unset($invoice_data);
				}
				break;
				
			CASE 'LABEL':
				if (is_array($label_data) && !empty($label_data) && isset($label_data['clickpost']) && $label_data['clickpost'] = 'Yes' && isset($label_data['label'])) {
					$Lable_Filename = $orderId.'.pdf';
					try{
						if (empty($label_path)) {
							$label_path = $path;
						} else {
							$label_path = $label_path.'/'; 
						}
						$path = $label_path.$Lable_Filename;
						$final_data = file_get_contents($label_data['label']);
						file_put_contents($path, $final_data);						
						$this->clickpost_copy_log("Generate Label File: $label_path".$Lable_Filename);
					} catch(Exception $e) {
						$this->clickpost_copy_log('Can not generate Label File');
					}
					unset($label_data);
				} else if(!empty($label_data) && is_string($label_data)) {
					$Lable_Filename = $orderId.'.pdf';
					try{
						$dompdf = new DOMPDF();
						$dompdf->load_html($label_data);
						$dompdf->set_paper('shipping_lable', 'portrait');
						$dompdf->render();
						
						if (empty($label_path)) {
							$label_path = $path;
						} else {
							$label_path = $label_path.'/'; 
						}
						$this->write_local($label_path, $Lable_Filename);
						$this->clickpost_copy_log("Generate Label File: $label_path".$Lable_Filename);
					} catch(Exception $e) {
						$this->clickpost_copy_log('Can not generate Label File');
					}
					unset($label_data);
				}
				break;
				
			CASE 'JOB_TICKET':
				if(!empty($job_ticket_data)) {
					$Jobticket_Filename = $orderId.'.pdf';
					try{
						$job_ticket_data = str_replace(SITE_URL, SITE_DOCUMENT_ROOT, $job_ticket_data);
	
						$dompdf = new DOMPDF();
						$dompdf->load_html($job_ticket_data);
						$dompdf->set_paper('a4', 'portrait');
						$dompdf->render();
						$this->write_local($path, $Jobticket_Filename);
						$this->clickpost_copy_log("Generate Job Ticket File: $path".$Jobticket_Filename);
					} catch(Exception $e) {
						$this->clickpost_copy_log('Can not generate Job Ticket File');
					}
					unset($job_ticket_data);
				}
				break;
			}
		return true;
	}
	public function write_local($filepath, $filename) {
		global $dompdf;
		file_put_contents($filepath.$filename, $dompdf->output());
	}
	// This function will delete orders invoice,jeb_ticket,label from the hot_folder
	public function deleteOrderFolder($orderId)
	{
		// Einvoice validate when ready to ship #219600
		try {
			$einvoice = new Einvoice();
			$einvoice->OrderEinvoice($orderId);
		} catch (Exception $e) {
			@error_log(strip_tags(print_r('Einvoice exception for ORder ' . $orderId . ' Date : ' . date("Y-m-d H:i:s"), TRUE)) . PHP_EOL, 3, DIR_WS_IMAGES . 'logs/einvoice.log');
			@error_log(strip_tags(print_r($e, TRUE)) . PHP_EOL, 3, DIR_WS_IMAGES . 'logs/einvoice.log');
		}
		
		//$path[1] = DIR_WS_IMAGES.'hot_folder/orders/invoice';
		$path[2] = DIR_WS_IMAGES . 'hot_folder/orders/job_ticket';
		$path[3] = DIR_WS_IMAGES . 'hot_folder/orders/label';
		foreach ($path as $key => $value) {
			$file_path = $value . '/' . $orderId . '.pdf';
			if (file_exists($file_path)) {
				$this->clickpost_copy_log("Deleted files $file_path");
				unlink($file_path);
			}
		}
		
		// PRINTSTOP :: Delet Barcode at Item Level - Task #223810
		require_once(DIR_WS_MODEL . 'OrderProductMaster.php');
		$ObjOrderProductMaster    = new OrderProductMaster();
		$ObjOrderProductMaster->setSelect("item_number");
		$ObjOrderProductMaster->setWhere("AND orders_products.orders_id = :orders_id ",$orderId,"int");
		$OrderProductDetails = $ObjOrderProductMaster->getOrderProduct();
		$path_item_barcode = DIR_WS_IMAGES . 'hot_folder/orders/item_barcode';
		foreach ( $OrderProductDetails as $OrderProduct ) {
			$file_path = $path_item_barcode . '/' . $OrderProduct['item_number'] . '.pdf';
			if (file_exists($file_path)) {
				$this->clickpost_copy_log("Deleted files $file_path");
				unlink($file_path);
			}
		}
	}
	public function generateClickpostLabel($wayBill, $order_id_type, $customers_name, $multiple_download_label = false, $return_html = false, $destinationOrderPath = ''){
		$response = $this->getShippinglabel($wayBill);
		if (isset($response['label']) && !empty($response['label'])) {
			// when we just need to pas sthe label url
			if ($return_html) {
				$data['label'] = $response['label'];
				$data['clickpost'] = 'Yes';
				return $data;
			}
			$final_data = file_get_contents($response['label']);
			$path =  $destinationOrderPath . "Lable" . $order_id_type . '_' . $customers_name . ".pdf";
			// when we have to download single file
			if ($multiple_download_label == false) {
				if(empty($destinationOrderPath)){
					$path = DIR_WS_IMAGES.$path;
				}
				file_put_contents($path, $final_data);
				if (file_exists($path))
				{
					header('Content-Description: File Transfer');
					header('Content-Type: application/octet-stream');
					header('Content-Disposition: attachment; filename='.basename($path));
					header('Expires: 0');
					header('Cache-Control: must-revalidate');
					header('Pragma: public');
					header('Content-Length: ' . filesize($path));
					ob_clean();
					flush();
					readfile($path);
					unlink($path);
					exit;
				}
			} else {
				// used to save clickpost label to given path in destinationOrderPath
				file_put_contents($path, $final_data);
			}
		}
	}
	public function clickpost_copy_log($msg) {
		@error_log(strip_tags(print_r($msg,TRUE)).PHP_EOL,3,$this->logFile);
	}
	/*
	*Task #206332 Gifting product :: PRINTSTOP
	*This function get if orderproduct contains ship to multiple location option or not
	*/
	public function getShipToMultipleLocation($orders_products_ids){
    
		require_once(DIR_WS_MODEL . 'OrderProductMaster.php');
		$ObjOrderProductMaster 	= new OrderProductMaster();
		$ObjOrderProductMaster->setSelect("orders_products_id,features_details")
		->setWhere("orders_products.orders_products_id IN @orders_products_id",explode(",",$orders_products_ids),"int");
		$DataOptionOrder = $ObjOrderProductMaster->getOrderProduct();
		$heading_array = array();
		foreach($DataOptionOrder as $product_option){
			$heading_array[] = unserialize($product_option['features_details']);
		}
		// Calculate vendor option price
		global $ship_to_multiple_location_option_name_arr;
		$ship_to_multiple_location = false;
		foreach ($heading_array as $key => $additional_options) {
			foreach ($additional_options as $additional_option) {
				if (in_array($additional_option['AttributeLabel'], $ship_to_multiple_location_option_name_arr)) {
					$ship_to_multiple_location = true;
				}
			}
		}
		return $ship_to_multiple_location;
	}	
}
?>

