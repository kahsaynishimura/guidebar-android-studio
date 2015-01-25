<?php

App::uses('AppController', 'Controller');

/**
 * Complaints Controller
 *
 * @property Complaint $Complaint
 * @property PaginatorComponent $Paginator
 */
class ComplaintsController extends AppController {

    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator', 'RequestHandler');

    function beforeFilter() {
        parent::beforeFilter();
        if ($this->request->is('ajax')) {
            if ($this->Auth->user() != NULL) {
                $this->Auth->allow('add');
            } else {
                return $response = json_encode(array('success' => false));
            }
        }
    }

    /**
     * add method
     *
     * @return void
     */
    public function add($id=null) {

        $this->autoRender = false;
        $event_id = $this->request->data['Complaint']['event_id'];
        $options = array(
            'conditions' => array('Complaint.event_id' => $event_id, 'Complaint.user_id' => $this->Auth->user('id')), //array of conditions
            'recursive' => -1, //int
        );
        $jaDenunciou = $this->Complaint->find('count', $options);
        if ($jaDenunciou > 0) {
            return $response = json_encode(array('success' => true, 'data' => 'Você já denunciou este evento. O promotor será notificado.'));
        }
        if (($this->request->is('post') && $this->RequestHandler->isAjax() && !empty($this->data)) || ($this->RequestHandler->ext === 'json')) {

            $this->Complaint->create();
            $this->request->data['Complaint']['user_id'] = $this->Auth->user('id');
            if ($this->Complaint->save($this->request->data)) {

//TODO				mSendEmailDenunciateTask = new SendEmailDenunciateTask();
//				mSendEmailDenunciateTask.execute((Void) null);
                return $response = json_encode(array('success' => true, 'data' => 'Obrigado pela contribuição. Sua denúncia foi recebida.'));
            }
        } else {
            return $response = json_encode(array('success' => false, 'data' => ''));
        }
        $this->set('response', $response);
    }

    public function isAuthorized($user) {
        if (!parent::isAuthorized($user)) {
            if ($this->action === 'add') {
                return true;
            }
        }
        return false;
    }

}
