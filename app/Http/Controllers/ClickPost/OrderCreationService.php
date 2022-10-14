<?php
namespace ClickPost;
include_once 'Object/NewOrder.php';
include_once 'Object/UserConfig.php';
use ClickPost\Object\NewOrder;
use ClickPost\Object\UserConfig;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 *
 * @author ashoksahara
 */
interface OrderCreationService {
    //put your code here
    
    public function createOrder(NewOrder $new_order, UserConfig $user_config);
}

?>
