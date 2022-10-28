<?php

namespace ClickPost\Object;


class UserConfig{
    private $user_name;
    private $key;
    
    function __construct($user_name, $key) {
        $this->user_name = $user_name;
        $this->key = $key;
    }
    
    public function getUser_name() {
        return $this->user_name;
    }

    public function getKey() {
        return $this->key;
    }

}

?>
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

