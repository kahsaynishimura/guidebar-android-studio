<?php

App::uses('AppController', 'Controller');

/**
 * States Controller
 *
 * @property State $State
 */
class StatesController extends AppController {

    public function index() {
        $this->set('states', $this->State->find('all', array('fields' => array('id', 'name'),'recursive'=>-1)));
    }

}
