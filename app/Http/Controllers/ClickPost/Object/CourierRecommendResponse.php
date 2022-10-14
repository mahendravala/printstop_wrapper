<?php
namespace ClickPost\Object;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CourierRecommendResponse{
    private $courier_company_name;
    private $courier_company_id;
    private $priority;
    
    function __construct($courier_company_name, $courier_company_id, $priority) {
        $this->courier_company_name = $courier_company_name;
        $this->courier_company_id = $courier_company_id;
        $this->priority = $priority;
    }
    
    function getCourier_company_name() {
        return $this->courier_company_name;
    }

    function getCourier_company_id() {
        return $this->courier_company_id;
    }

    function getPriority() {
        return $this->priority;
    }



}

?>

