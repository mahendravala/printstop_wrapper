<?php
namespace App\Http\Controllers\ClickPost;
include 'OrderCreationService.php';
include_once 'Object/NewOrder.php';
include_once 'Object/UserConfig.php';
include 'Exceptions/OrderCreationException.php';
include 'Object/OrderResponse.php';
require 'vendor/autoload.php'; 
use GuzzleHttp\Client;
use ClickPost\OrderCreationService;
use ClickPost\Object\NewOrder;
use ClickPost\Exceptions\OrderCreationException;
use ClickPost\Object\OrderResponse;
use ClickPost\Object\UserConfig;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class OrderCreationImpl implements OrderCreationService
{   
    public function createOrder($orderApiData)
    {
        $json_object = json_encode($orderApiData);
        echo "<pre>";
        print_r($json_object);
        exit;
        $client = new Client([
            'headers' => [ 'Content-Type' => 'application/json' ],
            'query' => ['key'=>'']
        ]);
        $response = $client->post('https://www.clickpost.in/api/v3/create-order/',
                ['body' => json_encode($json_object)]);
        if ($response->getStatusCode() != 200) {
            throw new OrderCreationException("Internal Server Error In Clickpost Server ",
                    $response->getStatusCode());
        }
        $this->parseMeta($response);
        return $this->parserResult($response);
        
    }
    
    private function parserResult($response_object)
    {
        $waybill = \GuzzleHttp\json_decode($response_object->getBody())->result->waybill;
        $shipping_url = \GuzzleHttp\json_decode($response_object->getBody())->result->label;
        return new OrderResponse($waybill, $shipping_url);
    }
    
    private function parseMeta($response_object)
    {
        $meta_object = \GuzzleHttp\json_decode($response_object->getBody())->meta;
        if ($meta_object->status != 200) {
            throw new OrderCreationException($meta_object->message, $meta_object->status);
        }
    }

}

?>

