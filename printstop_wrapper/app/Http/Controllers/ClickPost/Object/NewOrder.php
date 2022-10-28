<?php
namespace ClickPost\Object;
include_once 'Item.php';
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class NewOrder implements \JsonSerializable {

    private $drop_pincode;
    private $priority;
    private $drop_name;
    private $drop_state;
    private $invoice_number;
    private $drop_country;
    private $courier_partner;
    private $breadth;
    private $tin;
    private $height;
    private $code_value;
    private $pickup_name;
    private $weight;
    private $pickup_country;
    private $drop_address;
    private $order_type;
    private $invoice_value;
    private $drop_city;
    private $pickup_time;
    private $invoice_date;
    private $pickup_state;
    private $pickup_city;
    private $drop_phone;
    private $email;
    private $pickup_phone;
    private $delivery_type;
    private $pickup_address;
    private $pickup_pincode;
    private $reference_number;
    private $length;
    private $rvp_reason;

    function __construct($drop_pincode, $priority, $drop_name, $drop_state, $invoice_number, 
            $drop_country, $courier_partner, $breadth, $tin, $height, $code_value,
            $pickup_name, $weight, $pickup_country, $drop_address, $order_type, $invoice_value,
            $drop_city, $pickup_time, $invoice_date, $pickup_state, $pickup_city, $drop_phone,
            $email, $pickup_phone, $delivery_type, $pickup_address, $pickup_pincode, 
            $reference_number, $length, $rvp_reason) {
        $this->drop_pincode = $drop_pincode;
        $this->priority = $priority;
        $this->drop_name = $drop_name;
        $this->drop_state = $drop_state;
        $this->invoice_number = $invoice_number;
        $this->drop_country = $drop_country;
        $this->courier_partner = $courier_partner;
        $this->breadth = $breadth;
        $this->tin = $tin;
        $this->height = $height;
        $this->code_value = $code_value;
        $this->pickup_name = $pickup_name;
        $this->weight = $weight;
        $this->pickup_country = $pickup_country;
        $this->drop_address = $drop_address;
        $this->order_type = $order_type;
        $this->invoice_value = $invoice_value;
        $this->drop_city = $drop_city;
        $this->pickup_time = $pickup_time;
        $this->invoice_date = $invoice_date;
        $this->pickup_state = $pickup_state;
        $this->pickup_city = $pickup_city;
        $this->drop_phone = $drop_phone;
        $this->email = $email;
        $this->pickup_phone = $pickup_phone;
        $this->delivery_type = $delivery_type;
        $this->pickup_address = $pickup_address;
        $this->pickup_pincode = $pickup_pincode;
        $this->reference_number = $reference_number;
        $this->length = $length;
        $this->rvp_reason = $rvp_reason;
    }

    function getDrop_pincode() {
        return $this->drop_pincode;
    }

    function getPriority() {
        return $this->priority;
    }

    function getDrop_name() {
        return $this->drop_name;
    }

    function getDrop_state() {
        return $this->drop_state;
    }

    function getInvoice_number() {
        return $this->invoice_number;
    }

    function getDrop_country() {
        return $this->drop_country;
    }

    function getCourier_partner() {
        return $this->courier_partner;
    }

    function getBreadth() {
        return $this->breadth;
    }

    function getTin() {
        return $this->tin;
    }

    function getHeight() {
        return $this->height;
    }

    function getCode_value() {
        return $this->code_value;
    }

    function getPickup_name() {
        return $this->pickup_name;
    }

    function getWeight() {
        return $this->weight;
    }

    function getPickup_country() {
        return $this->pickup_country;
    }

    function getDrop_address() {
        return $this->drop_address;
    }

    function getOrder_type() {
        return $this->order_type;
    }

    function getInvoice_value() {
        return $this->invoice_value;
    }

    function getDrop_city() {
        return $this->drop_city;
    }

    function getPickup_time() {
        return $this->pickup_time;
    }

    function getInvoice_date() {
        return $this->invoice_date;
    }

    function getPickup_state() {
        return $this->pickup_state;
    }

    function getPickup_city() {
        return $this->pickup_city;
    }

    function getDrop_phone() {
        return $this->drop_phone;
    }

    function getEmail() {
        return $this->email;
    }

    function getPickup_phone() {
        return $this->pickup_phone;
    }

    function getDelivery_type() {
        return $this->delivery_type;
    }

    function getPickup_address() {
        return $this->pickup_address;
    }

    function getPickup_pincode() {
        return $this->pickup_pincode;
    }

    function getReference_number() {
        return $this->reference_number;
    }

    function getLength() {
        return $this->length;
    }

    function getRvp_reason() {
        return $this->rvp_reason;
    }

    public function jsonSerialize() {
        return [
            'reference_number' => $this->getReference_number(),
            'invoice_date' => $this->getInvoice_date(),
            'drop_address' => $this->getDrop_address(),
            'priority' => $this->getPriority(),
            'courier_partner' => $this->getCourier_partner(),
            'invoice_value' => $this->getInvoice_value(),
            'delivery_type' => $this->getDelivery_type(),
            'pickup_country' => $this->getPickup_country(),
            'breadth' => $this->getBreadth(),
            'height' => $this->getHeight(),
            'length' => $this->getLength(),
            'cod_value' => $this->getCode_value(),
            'drop_phone' => $this->getDrop_phone(),
            'drop_name' => $this->getDrop_name(),
            'email' => $this->getEmail(),
            'drop_city' => $this->getDrop_city(),
            'drop_country' => $this->getDrop_country(),
            'invoice_number' => $this->getInvoice_number(),
            'rvp_reason' => $this->getRvp_reason(),
            'order_type' => $this->getOrder_type(),
            'items' => "[{\"sku\": \"XYZ1\", \"quantity\": 1, \"description\": \"item1\", \"price\": 200}, {\"sku\": \"XYZ2\", \"quantity\": 1, \"description\": \"item2\", \"price\": 300}, {\"sku\": \"XYZ3\", \"quantity\": 2, \"description\": \"item3\", \"price\": 400}]",
            'drop_state' => $this->getDrop_state(),
            'drop_pincode' => $this->getDrop_pincode(),
            'weight' => $this->getWeight(),
            'pickup_time' => $this->getPickup_time(),
            'pickup_address' => $this->getPickup_address(),
            'tin' => $this->getTin(),
            'pickup_phone' => $this->getPickup_phone(),
            'pickup_pincode' => $this->getPickup_pincode(),
            'pickup_city' => $this->getPickup_city(),
            'pickup_state' => $this->getPickup_state(),
            'pickup_name' => $this->getPickup_name()
        ];
    }

}
?>

