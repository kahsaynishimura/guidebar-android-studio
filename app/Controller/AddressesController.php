<?php

App::uses('AppController', 'Controller');

/**
 * Addresses Controller
 *
 * @property Address $Address
 * @property PaginatorComponent $Paginator
 */
class AddressesController extends AppController {

    public $helpers = array('Js');

    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator');

    function beforeFilter() {
        parent::beforeFilter();
        if ($this->request->is('ajax')) {
            if ($this->Auth->user() != NULL) {
                $this->Auth->allow('view');
            }
        }
    }

    public function view($id) {
        $this->autoLayout = false;
        $this->autoRender = false;
        if (!$this->Address->exists($id)) {
            throw new NotFoundException(__('Endereço inválido'));
        }

        if ($this->request->is('post')) {

            $this->Address->Behaviors->load('Containable');
            $address = $this->Address->find('first', array(
                'conditions' => array('Address.id' => $id),
                'order' => array('Address.place_name' => 'asc'),
                'contain' => array(
                    'City' => array(
                        'fields' => array('state_id','name'),
                        'State'=>array(
                            'fields'=>array('uf')
                        )
                    )
                )
            ));
            return json_encode( $address);
        }
    }

    /**
     * edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function edit($id = null) {
        if (!$this->Address->exists($id)) {
            throw new NotFoundException(__('Endereço inválido'));
        }
        if ($this->request->is('post')) {
            $state_id = $this->request->data['Address']['state_id'];
        } else {
            $state_id = '1';
        }
        if ($this->request->is('post') || $this->request->is('put')) {

            if ($this->Address->save($this->request->data)) {
                $this->Session->setFlash(__('O endereço foi salvo.'));
                return $this->redirect(array('controller' => 'events', 'action' => 'index'));
            } else {
                $this->Session->setFlash(__('O endereço não foi salvo. Por favor, verifique as instruções e tente novamente.'));
            }
        } else {
            $options = array('conditions' => array('Address.' . $this->Address->primaryKey => $id));
            $endereco = $this->Address->find('first', $options);
            $this->request->data = $endereco;
            $state_id = $endereco['City']['state_id'];
            $this->request->data['Address']['state_id'] = $state_id;
        }
        $this->loadModel('State');
        $states = $this->State->find('list');
        $cities = $this->Address->City->find('list', array(
            'conditions' => array('City.state_id' => $state_id),
            'recursive' => -1
                )
        );

        $this->set(compact('cities', 'states'));
    }

    public function isAuthorized($user) {
        if (!parent::isAuthorized($user)) {
            if ($this->action === 'view') {
                return true;
            }
            if (in_array($this->action, array('edit'))) {
                $addressId = $this->request->params['pass'][0];
                $options = array('conditions' => array('Address.' . $this->Address->primaryKey => $addressId));
                $address = $this->Address->find('first', $options);
                return $this->Address->Event->isOwnedBy($address['Event']['id'], $user['id']);
            }
        }
        return false;
    }

}
