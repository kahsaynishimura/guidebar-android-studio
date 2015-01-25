<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


foreach ($states as &$state) {
    unset($state['State']['generated_html']);
}
echo json_encode(compact('states'));