<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ClickPost\Object;

/**
 * Description of Item
 *
 * @author ashoksahara
 */
class Item implements \JsonSerializable{
    private $name;
    private $sku;
    private $quantity;
    private $descritption;
    private $price;
            
    function __construct($name, $sku, $quantity, $descritption, $price) {
        $this->name = $name;
        $this->sku = $sku;
        $this->quantity = $quantity;
        $this->descritption = $descritption;
        $this->price = $price;
    }

    function getName() {
        return $this->name;
    }

    function getSku() {
        return $this->sku;
    }

    function getQuantity() {
        return $this->quantity;
    }

    function getDescritption() {
        return $this->descritption;
    }
    
    function getPrice() {
        return $this->price;
    }

    
        
    //put your code here
    public function jsonSerialize() {
        return [
            'name'=>$this->getName(),
            'description'=>$this->getDescritption(),
            'price'=>$this->getPrice(),
            'quantity'=>$this->getQuantity(),
            'sku'=> $this->getSku()
        ];
    }

}
