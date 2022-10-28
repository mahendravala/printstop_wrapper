<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use App\Http\Controllers\Clickpost\CourierRecommendImpl;
use Session;
use GuzzleHttp\Client;
use App\FileOperation;
use App\Models\PickupRequest;
use DB;
use Artisan;
use App\Http\Controllers\ClickPost\OrderCreationImpl;
use App\Jobs\RecAndManifest;
use File;
use Response;

class ClickpostController extends Controller
{
    const API_KEY = "5f0e9f97-6c01-4d70-acc3-d23aa426c682";
    const USER_NAME = "printstop-test";
    const API_BASE_URL = "https://www.clickpost.in/";
    const API_VERSION_ONE = "api/v1/";
    const API_VERSION_TWO = "api/v2/";
    const API_VERSION_THREE = "api/v3/";
    const COURIER_API_URL = self::API_BASE_URL . self::API_VERSION_ONE . "recommendation_api/?key=" . self::API_KEY;
    const ORDER_CREATE_API_URL = self::API_BASE_URL . self::API_VERSION_THREE . "create-order/?key=" . self::API_KEY;
    const GET_SHIPPING_LABEL_URL = self::API_BASE_URL . self::API_VERSION_ONE . "fetch/shippinglabel/?key=" . self::API_KEY;
    const TRACK_ORDER_URL = self::API_BASE_URL . self::API_VERSION_TWO . "track-order/?key=" . self::API_KEY . '&username=' . self::USER_NAME;
    const COURIER_CHOSE_FROM = 6; // will pic first 2 couriers from response  
    // const GENERATE_ORDER_PATH = [DIR_WS_IMAGES . 'hot_folder/orders', DIR_WS_IMAGES . 'hot_folder/orders/invoice', DIR_WS_IMAGES . 'hot_folder/orders/job_ticket', DIR_WS_IMAGES . 'hot_folder/orders/label'];
    // properties
    public $pickup_pincode;
    public $order_type;
    public $reference_number;
    public $invoice_value;
    public $orderId;
    public $shippingMode;
    public $heigh;
    public $breadth;
    public $lenght;
    public $cod_value;
    public $pickup_time;
    public $pickup_address;
    public $pickup_phone;
    public $pickup_name;
    public $delivery_type;
    public $pickup_state;
    public $pickup_email;
    public $pickup_city;
    public $tin;
    public $pickup_country;
    public $shipping_type;
    public $allow_clickpost;
    public $otp_service;
    public $return_address;
    public $return_pincode;
    public $return_state;
    public $return_phone;
    public $return_name;
    public $return_city;
    public $return_country;

    public function __construct() {

        // Storage::disk("local")->put($path, $contents);
        DB::enableQueryLog();
                // pickup_info
        $this->pickup_state     = 'Maharashtra'; 
        $this->pickup_address   = 'PrintStop India Pvt. Ltd. 1st floor, gala no 9 B6 Bldg, Bhumi World Near Kalyan Naka, Nh3, Pimplas Ta. Bhiwandi, Thane, Maharashtra 421302';
        $this->pickup_email     = 'rahul@printstop.co.in'; // :: Niks need to change
        $this->pickup_time      = date("Y-m-d").'T18:00:00+05:30';
        $this->pickup_pincode   = '421302'; 
        $this->pickup_city      = 'Bhiwandi';
        $this->tin              = '27AAECP1377J1Z6'; // :: Niks need to change from here 
        $this->pickup_name      = 'Printstop India Pvt. Ltd';
        $this->pickup_country   = 'IN';
        $this->pickup_phone     = '7045848448';
        $this->return_address   = '1st floor, gala no 9 B6 Bldg, Bhumi World Near Kalyan Naka, Nh3, Pimplas Ta. Bhiwandi, Thane, Maharashtra 421302';
        $this->return_pincode   = '421302';
        $this->return_state     = 'Maharashtra';
        $this->return_phone     = '+91 70458 48448';
        $this->return_name      = 'Rahul Kurne';
        $this->return_city      = 'Bhiwandi';
        $this->return_country   = 'IN';
        // shipment_details static fields
        $this->order_type       = 'PREPAID';
        $this->heigh            = '10';
        $this->breadth          = '10';
        $this->lenght           = '10';
        $this->cod_value        = '0'; // :: Niks need to remove from here
        $this->delivery_type    = 'FORWARD';
        $this->description      = 'Group Products';
        $this->otp_service      = 0;
    }

    public function index()
    {
        // clickpost
        $crs = "test";
        return view('clickpost');
    }

    public function downloadPdf(Request $request)
    {
        // clickpost
        $zip = new \ZipArchive();
        $file_id = $request->id;
        $query = FileOperation::query();
        $query->where('id', $file_id);
        $file = $query->first();
        $folder = date("Y_m_d_H_i_s", strtotime($file->uploaded_at));

        $fileName = $folder.'.zip';
        if ($zip->open(public_path("uploads/$fileName"), \ZipArchive::CREATE)== TRUE)
        {
            $files = File::files(public_path("uploads/$folder"));
            foreach ($files as $key => $value){
                $relativeName = basename($value);
                $zip->addFile($value, $relativeName);
            }
            $zip->close();
        }

        return response()->download(public_path("uploads/$fileName"));
    }


    public function downloadExcel(Request $request)
    {
            // clickpost
        $file_id = $request->id;
        $query = PickupRequest::query();
        $query->where('file_id', $file_id);
        $records = $query->get();

        $filename = time().".csv";
        $handle = fopen(public_path("uploads/$filename"), 'w+');
        fputcsv($handle, array('date', 'id', 'order_status', 'comment', 'type_of_order', 'internal_notes', 'shipping_company', 'tracking_number', 'name', 'email_id', 'product', 'ctype', 'customer_notify', 'orders_due_date', 'cp_id','user_type','customer_name'));

        foreach($records as $record) {
            fputcsv($handle, array(
                '', $record['order_id'], 'Shipped', '','','', $record['shipping_company'], $record['awb'], '', '', '', 
                '', '', '', $record['cp_id'], $record['user_type'], $record['customer_name']
                )
            );
        }

        fclose($handle);

        $headers = array(
            'Content-Type' => 'text/csv',
        );

        return Response::download(public_path("uploads/$filename"), $filename, $headers);
    }

    public function call_the_command(Request $request)
    {
        // clickpost
        Artisan::call('run:manifest',['file_id' => $request->id]);
        return redirect("clickpost/recommendations");
    }

    public function delete(Request $request)
    {
        $file_id = $request->id;
        $query = FileOperation::query();
        $query->where('id', $file_id);
        $file = $query->first();
        $folder = date("Y_m_d_H_i_s", strtotime($file->uploaded_at));
        $files = File::deleteDirectory(public_path("uploads/$folder"));
        PickupRequest::where('file_id', $file_id)->delete();        
        FileOperation::where('id', $file_id)->delete();
        return redirect("clickpost/recommendations");
    }

    private function csv_to_array($filename='', $delimiter=',')
    {
        if(!file_exists($filename) || !is_readable($filename))
            return FALSE;

        $header = NULL;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== FALSE)
        {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
            {
                if(!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }
        return $data;
    }


    public function post(Request $request)
    {
        
        $file = $request->file('exampleInputFile');
        $id = $this->uploadFile($file);
        
        $FileOperations = new FileOperation();
        $data = $FileOperations->where('id',$id)->get();
        $filepath = $data[0]->file;
        // create records by lines

        $recommend_data = array();
        $csv = $this->csv_to_array($filepath);
        // echo "<pre>";print_r($csv); exit;
        foreach ($csv as $key => $value) {
            $value['file_id'] = $id;
            $value['manifest_status'] = 0;
            $value['cod_value'] = 0;
            $value['sku'] = '100';
            $value['invoice_number'] = $value['order_id'];
            $pickup_request = new PickupRequest();
            $pickup_request->insert([$value]);
        }
        RecAndManifest::dispatch(['id' => $id]);

        return redirect("clickpost/recommendations");
    }

    public function getCourierCompany($recommend_data, $key)
    {
        $client = new Client([
            'headers' => [ 'Content-Type' => 'application/json' ],
            'query'=>['key'=>$key]
        ]);
        
        $response = $client->post('https://www.clickpost.in/api/v1/recommendation_api/',
                ['body' => json_encode([$recommend_data])]);

        $response = $this->parseMeta($response);
        
        return $this->parseClickPostResponse($response);
    }
    
    public static function parseClickPostResponse($response_body)
    {
        $courier_recommend_array = new \ArrayObject();
        $preference_array = \GuzzleHttp\json_decode($response_body->getBody())->result[0]->preference_array;

        return $preference_array;
    }
    
    public static function parseMeta($response_body)
    {
        $meta_object = \GuzzleHttp\json_decode($response_body->getBody())->meta;

        if ($meta_object->status != 200) {
            throw new \Exception($meta_object->message,
                    $meta_object->status);
        }
        return $meta_object;
    }

    public function createOrder($orderApiData)
    {
        $client = new Client([
            'headers' => [ 'Content-Type' => 'application/json' ],
            'query' => [
                'key'=>'5f0e9f97-6c01-4d70-acc3-d23aa426c682',
                'username' => 'printstop-test'
            ]
        ]);
        $response = $client->post('https://www.clickpost.in/api/v3/create-order/',
                ['body' => json_encode($orderApiData)]);
        if ($response->getStatusCode() != 200) {
            throw new OrderCreationException("Internal Server Error In Clickpost Server ",
                    $response->getStatusCode());
        }

        return $response;
        
    }

   /**
    * prepareOrderData() is Used to prepare orderdata that we need to send to clickpost via api
    *
    * @access public
    * @param int $cp_id required
    * @return Array
    */
    public function prepareOrderData($cp_id, $orderData)
    {
        $order = $pickup_info = $drop_info = $shipment_details = $additional = [];
        $weight = ($orderData->total_weight > 0) ? (int)($orderData->total_weight * 1000 ) : 1000 ;
        $this->invoice_value = (float)$orderData->price;         
        // static values
        $pickup_info['pickup_state']    = $this->pickup_state;
        $pickup_info['pickup_address']  = $this->pickup_address;
        $pickup_info['email']           = $this->pickup_email;
        $pickup_info['pickup_time']     = $this->pickup_time; // Niks need to check or Ask 
        $pickup_info['pickup_pincode']  = $this->pickup_pincode;
        $pickup_info['pickup_city']     = $this->pickup_city;
        $pickup_info['pickup_name']     = $this->pickup_name;
        $pickup_info['pickup_country']  = $this->pickup_country;
        $pickup_info['pickup_phone']    = $this->pickup_phone; 
        $pickup_info['order_type']      = $this->order_type;
        // delivery information 
        $drop_address = $orderData->drop_address;
            
        $drop_info['drop_address']      = $drop_address;
        $drop_info['drop_phone']        = $orderData->drop_phone;
        $drop_info['drop_country']      = $orderData->drop_country;
        $drop_info['drop_state']        = $orderData->drop_state;
        $drop_info['drop_pincode']      = $orderData->drop_pincode;
        $drop_info['drop_city']         = $orderData->drop_city;
        $drop_info['drop_name']         = $orderData->drop_name;
        $drop_info['drop_email']        = $orderData->drop_email;

        // Shipment order details
        $shipment_details['height']             = $orderData->height;
        $shipment_details['order_type']         = $orderData->order_type;
        $shipment_details['invoice_value']      = $orderData->invoice_value;
        $shipment_details['invoice_number']     = $orderData->order_id;
        $shipment_details['invoice_date']       = date('Y-m-d', strtotime($orderData->invoice_date));
        $shipment_details['reference_number']   = $orderData->reference_number.time();
        $shipment_details['length']             = $orderData->length;
        $shipment_details['breadth']            = $orderData->breadth;
        $shipment_details['weight']             = $orderData->weight; 
        $shipment_details['items']              = [["price"=>$orderData->invoice_value,"description"=>"PID : ".$orderData->description,"sku"=>$orderData->sku,"quantity"=>$orderData->quantity,"weight"=>$orderData->weight]]; // Json list with multiple item objects in it 
        $shipment_details['cod_value']          = $this->cod_value; 
        $shipment_details['courier_partner']    = $cp_id;

        // Additional information 
        $additional['label']                    = true;
        $additional['delivery_type']            = $this->delivery_type;
        $additional['async']                    = 0;            
        $additional['account_code']             = $this->shippingMode;
        $additional['order_date']               = date("Y-m-d");
        $additional['order_id']                 = (string)$orderData->order_id;
        $additional['return_info']['pincode']   = $this->return_pincode;
        $additional['return_info']['address']   = $this->return_address;
        $additional['return_info']['state']     = $this->return_state;
        $additional['return_info']['phone']     = $this->return_phone; 
        $additional['return_info']['name']      = $this->return_name; 
        $additional['return_info']['city']      = $this->return_city;
        $additional['return_info']['country']   = $this->return_country;
        
        $order['pickup_info']       = $pickup_info;
        $order['drop_info']         = $drop_info;
        $order['additional']        = $shipment_details;
        $order['shipment_details']  = $additional;
        return $order;
    }


    public function recommendations(Request $request)
    {
        $fileOperations = FileOperation::orderBy('id', 'desc')->limit(30)->get();
        $files = [];
        foreach ($fileOperations as $key => $value) {
            $query = PickupRequest::query();
            $query->where('file_id', $value->id);
            $value['total_count'] = $query->count();

            $query2 = PickupRequest::query();
            $query2->where('file_id', $value->id);
            $query2->where('manifest_status', 1);
            $value['success_count'] = $query2->count();
            $value['file_id'] = $value->id;

            $files[] = $value;

        }
        return view('recommendation',["files"=>$files]);
    }
    
    public function uploadFile($file)
    {
        // File Details 
        $filename = date("Ymdhis")."-".$file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $tempPath = $file->getRealPath();
        $fileSize = $file->getSize();
        $mimeType = $file->getMimeType();

        // Valid File Extensions
        $valid_extension = array("csv");

        // 2MB in Bytes
        $maxFileSize = 2097152; 

        // Check file extension
        if(in_array(strtolower($extension),$valid_extension)) {

            // Check file size
            if ($fileSize <= $maxFileSize) {

              // File upload location
              $location = 'uploads';

              // Upload file
              $file->move($location,$filename);

              // Import CSV to Database
              $filepath = public_path($location."/".$filename);

              // Insert to MySQL database
              $FileOperations = new FileOperation();
              $FileOperations->file = $filepath;
              $FileOperations->save();
              return $FileOperations->id;
              
            } else {
              
            }

        }

    }
}
