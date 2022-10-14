<?php
namespace ClickPost;
include 'CourierRecommendService.php';
require 'vendor/autoload.php'; 
include 'Object/CourierRecommendResponse.php';
include 'Exceptions/CourierRecommendationException.php';
use GuzzleHttp\Client;
use \ClickPost\Object\CourierRecommendData;
use \ClickPost\Object\CourierRecommendResponse;
use \ClickPost\Exceptions\CourierRecommendationException;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CourierRecommendImpl implements CourierRecommendService{
    

    public function getCourierCompany(CourierRecommendData $recommend_data, $key) {
        $client = new Client([
            'headers' => [ 'Content-Type' => 'application/json' ],
            'query'=>['key'=>$key]
        ]);
        $response = $client->post('https://www.clickpost.in/api/v1/recommendation_api/',
                ['body' => json_encode($recommend_data->jsonSerialize())]);
        $this->parseMeta($response);
        return $this->parseClickPostResponse($response);
    }
    
    private function parseClickPostResponse($response_body){
        $courier_recommend_array = new \ArrayObject();
        $preference_array = \GuzzleHttp\json_decode($response_body->getBody())->result[0]->preference_array;
        foreach ($preference_array as $preference){
            $courier_recommend_response = new CourierRecommendResponse($preference->cp_name,
                    $preference->cp_id,$preference->priority);
            $courier_recommend_array->append($courier_recommend_response);
        }
        return $courier_recommend_array;
    }
    
    private function parseMeta($response_body){
        $meta_object = \GuzzleHttp\json_decode($response_body->getBody())->meta;
        if ($meta_object->status != 200){
            throw new CourierRecommendationException($meta_object->message,
                    $meta_object->status);
        }
    }
  
}

?>
