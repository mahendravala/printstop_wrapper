<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PickupRequest;
use App\FileOperation;
use DB;
use App\Http\Controllers\ClickpostController;
use GuzzleHttp\Client;
use File;

class RecAndManifest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:manifest {file_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will run recommendation and manifest command!';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $file_id = $this->argument('file_id');
        $query = PickupRequest::query();
        $query->where('file_id', $file_id);
        $query->where('manifest_status', '0');
        $records = $query->get();

        $query = FileOperation::query();
        $query->where('id', $file_id);
        $file = $query->first();

        foreach ($records as $key => $record) {
            $folder = date("Y_m_d_H_i_s", strtotime($file->uploaded_at));
            File::ensureDirectoryExists(public_path("uploads/$folder"));

            // code...
            $order = $pickup_info = $drop_info = $shipment_details = $additional = [];
            $record['pickup_pincode'] = '400013';

            $client = new Client([
                'headers' => [ 'Content-Type' => 'application/json' ],
                'query'=>[
                    'key'=>'5f0e9f97-6c01-4d70-acc3-d23aa426c682'
                ]
            ]);
            $rec_req = [];
            $rec_req["pickup_pincode"]  = $record['pickup_pincode'];
            $rec_req["drop_pincode"]    = $record['drop_pincode'];
            $rec_req["order_type"]      = $record['order_type'];
            $rec_req["reference_number"] = $record['reference_number'];
            $rec_req["invoice_value"]   = $record['invoice_value'];
            $rec_req["weight"]          = $record['weight'];
            $rec_req["delivery_type"]   = "FORWARD";

            $rec_req["additional"]['custom_fields'][] = ["key" => "shipping_type", "value"=>$record['shipping_type']];
            $rec_req["additional"]['custom_fields'][] = ["key" => "is_otp_enabled", "value"=>$record['is_otp_enabled']];
            $rec_req["additional"]['custom_fields'][] = ["key" => "corporate_id", "value"=> '0'];
            $rec_req["additional"]['custom_fields'][] = ["key" => "products_id", "value"=> $record['sku']];

            
            // [{"pickup_pincode":"421302","drop_pincode":"401107","order_type":"PREPAID","reference_number":"5598071663223967","invoice_value":395,"weight":500,"delivery_type":"FORWARD","additional":{"custom_fields":[{"key":"shipping_type","value":"64"},{"key":"is_otp_enabled","value":1},{"key":"corporate_id","value":288},{"key":"products_id","value":"6827"}]}}]

            $response = $client->post('https://www.clickpost.in/api/v1/recommendation_api/',
                    ['body' => json_encode([$rec_req])]);
            $rec_response = \GuzzleHttp\json_encode(\GuzzleHttp\json_decode($response->getBody())->result);
    
            $meta_object = ClickpostController::parseMeta($response);

            if ($meta_object->status == 200) {

                PickupRequest::where('id',$record->id)->update(['rec_response'=> ($rec_response)]);

                $clickpostManifest = new ClickpostController();

                $preferences = ClickpostController::parseClickPostResponse($response);

                foreach ($preferences as $key => $preference) {
                    
                    $clickpostManifest->shippingMode = $preference->account_code;

                    $orderData = $clickpostManifest->prepareOrderData($preference->cp_id, $record);
                    
                    $response_body = $clickpostManifest->createOrder($orderData);

                    $meta_object = \GuzzleHttp\json_decode($response_body->getBody())->meta;
                    
                    if ($meta_object->status != 200) {
                        continue;
                    }

                    $response_array = \GuzzleHttp\json_decode($response_body->getBody())->result;
                    
                    $waybill = $response_array->waybill;
                    $shipping_url = $response_array->label;

                    $filename = basename($shipping_url);
                    $image = file_get_contents($shipping_url);

                    file_put_contents(public_path("uploads/$folder/$filename"), $image);

                    PickupRequest::where('id',$record->id)->update(
                        [
                            'manifest_response' => json_encode($response_array),
                            'shipping_company' => $preference->account_code,
                            'cp_id' => $preference->cp_id, 'awb' => $waybill,
                            'shipping_url' => $shipping_url,
                            'manifest_status' => 1
                        ]
                    );
                    continue;
                }
            }
        }
        return 0;
    }
}
