<?php

/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5 
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Controller', 'Controller');
App::import('Core', 'l10n'); 
/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

    public $helpers = array('Html', 'Form', 'Js');
    public $components = array(
        'Session', 'RequestHandler', 'AjaxMultiUpload.Upload', 
        'Auth' => array(
            'loginRedirect'=>array('controller'=>'events','action'=>'index'),
            'authorize' => array('Controller'),
            'authenticate' => array(
                'Form' => array(
                    'fields' => array('username' => 'email')
                )
            )
        )
    );

    public function isAuthorized($user) {
        return false;
    }

    function beforeFilter() {
        parent::beforeFilter();
        CakeLog::write('info', "Requisição: " . $this->request->clientIp() . '  ' . CakeRequest::host() . " - " .
                 $this->here, 'request');
        $this->Auth->allow('index', 'view','display');
        if ((!($this->request->is('ajax'))) && $this->RequestHandler->ext === 'json' && $this->request->is('post') && $this->request->action != 'login' &&
                (!($this->request->action == 'loginfb' && $this->RequestHandler->ext === 'json'))) {
            $this->loadModel('User');
            $userMobile = $this->User->find('first', array(
                'fields' => array('name', 'is_active', 'email', 'id'),
                'conditions' => array(
                    'email' => $this->request->data['email'],
                    'access_token' => $this->request->data['token']
                ))
            );
            if (!(empty($userMobile['User']))) {
                $this->Auth->login($userMobile['User']);
            }
        }
    }

    public function beforeRender() {
        $user['User'] = $this->Auth->user();
        $this->set('user_login', $user);
    }

}
