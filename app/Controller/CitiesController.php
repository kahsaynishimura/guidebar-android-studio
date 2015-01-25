<?php

App::uses('AppController', 'Controller');

/**
 * Cities Controller
 *
 * @property City $City
 */
class CitiesController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('getByStateSearch');
    }

    public function getByStateSearch() {
        $state_id = $this->request->data['Event']['state_id'];
        if ($this->RequestHandler->ext === 'json') {
            $cidades = $this->City->find('all', array(
                'conditions' => array('City.state_id' => $state_id),
                'recursive' => -1,
                'fields' => array('id', 'name', 'state_id')
            ));
            $this->set('cities', $cidades);
        } else {

            $cidades = $this->City->find('list', array(
                'conditions' => array('City.state_id' => $state_id),
                'recursive' => -1
            ));
            $this->autoLayout = FALSE;
            $this->autoRender = FALSE;
            $this->set('cities', $cidades);
            $this->render('get_by_state', 'ajax');
        }
    }

    public function getByState() {
        $state_id = $this->request->data['Address']['state_id'];

        $cidades = $this->City->find('list', array(
            'conditions' => array('City.state_id' => $state_id),
            'recursive' => -1
        ));

        $this->set('cities', $cidades);
        $this->layout = 'ajax';
    }

    public function isAuthorized($user) {
        if (!parent::isAuthorized($user)) {
            if (in_array($this->action, array('getByState'))) {

                return true;
            }
        }
        return false;
    }

}
