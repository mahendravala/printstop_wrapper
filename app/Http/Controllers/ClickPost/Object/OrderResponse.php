<?php
namespace ClickPost\Object;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class OrderResponse implements \JsonSerializable{
    private $way_bill;
    private $shipping_url;
    
    function __construct($way_bill, $shipping_url) {
        $this->way_bill = $way_bill;
        $this->shipping_url = $shipping_url;
    }
    
    function getWay_bill() {
        return $this->way_bill;
    }

    function getShipping_url() {
        return $this->shipping_url;
    }

    public function jsonSerialize() {
        return [
            'waybill'=>$this->getWay_bill(),
            'shipping_url'=>$this->getShipping_url()
            ];
    }

}

?>

