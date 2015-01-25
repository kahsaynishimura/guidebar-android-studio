<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

foreach ($purchases as &$purchase) {
    unset($purchase['Purchase']['generated_html']);
}
echo json_encode(compact( 'purchases'));
