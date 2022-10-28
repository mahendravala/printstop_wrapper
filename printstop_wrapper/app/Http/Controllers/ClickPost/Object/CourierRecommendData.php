<?php

namespace ClickPost\Object;
class CourierRecommendData implements \JsonSerializable{
    private $pickup_pincode;
    private $drop_pincode;
    private $order_type;
    private $invoice_value;
    private $item_name;
    private $length;
    private $height;
    private $weight;
    private $delivery_type;
    private $reference_number;
    private $breadth;
    
    public function __construct($pickup_pincode, $drop_pincode, $order_type, $invoice_value, $item_name,
            $length, $height, $weight, $delivery_type, $reference_number, $breadth) {
        $this->pickup_pincode = $pickup_pincode;
        $this->drop_pincode = $drop_pincode;
        $this->order_type = $order_type;
        $this->invoice_value = $invoice_value;
        $this->item_name = $item_name;
        $this->length = $length;
        $this->height = $height;
        $this->weight = $weight;
        $this->delivery_type = $delivery_type;
        $this->reference_number = $reference_number;
        $this->breadth = $breadth;
    }

    function getPickup_pincode() {
        return $this->pickup_pincode;
    }

    function getDrop_pincode() {
        return $this->drop_pincode;
    }

    function getOrder_type() {
        return $this->order_type;
    }

    function getInvoice_value() {
        return $this->invoice_value;
    }

    function getItem_name() {
        return $this->item_name;
    }

    function getLength() {
        return $this->length;
    }

    function getHeight() {
        return $this->height;
    }

    function getWeight() {
        return $this->weight;
    }

    function getDelivery_type() {
        return $this->delivery_type;
    }

    function getReference_number() {
        return $this->reference_number;
    }

    function getBreadth() {
        return $this->breadth;
    }

    public function jsonSerialize() {
        return [[
            'pickup_pincode'=>$this->getPickup_pincode(),
            'drop_pincode' => $this->getDrop_pincode(),
            'order_type'=>$this->getOrder_type(),
            'invoice_value'=>$this->getInvoice_value(),
            'item'=>$this->getItem_name(),
            'length'=>$this->getLength(),
            'height'=>$this->getHeight(),
            'weight'=>$this->getWeight(),
            'delivery_type'=>$this->getDelivery_type(),
            'reference_number'=>$this->getReference_number(),
            'breadth'=>$this->getBreadth()
        ]];
    }

}

?>

