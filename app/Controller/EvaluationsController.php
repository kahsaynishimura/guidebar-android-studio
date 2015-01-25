<?php

App::uses('AppController', 'Controller');

/**
 * Evaluations Controller
 *
 * @property Evaluation $Evaluation
 * @property PaginatorComponent $Paginator
 */
class EvaluationsController extends AppController {

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
    public function add() {
        $this->autoRender = false;
        if (($this->request->is('post') && $this->RequestHandler->isAjax() && !empty($this->data)) || ($this->RequestHandler->ext === 'json')) {
            $nroAvaliacoes = $this->Evaluation->find('count', array('conditions' => array(
                    'Evaluation.event_id' => $this->request->data['Evaluation']['event_id']
            )));
            $options = array('conditions' => array(
                    'Evaluation.user_id' => $this->Auth->user('id'),
                    'event_id' => $this->request->data['Evaluation']['event_id']
            ));
            $evaluation = $this->Evaluation->find('first', $options);
            $jaAvaliou = false;
            if ($evaluation == NULL) {
                $this->Evaluation->create();
            } else {
                $jaAvaliou = true;
                $this->request->data['Evaluation']['id'] = $evaluation['Evaluation']['id'];
            }
            $this->request->data['Evaluation']['user_id'] = $this->Auth->user('id');

            if ($this->Evaluation->save($this->request->data)) {
                if ($nroAvaliacoes == 0) {
                    $this->Evaluation->Event->id = $this->request->data['Evaluation']['event_id'];
                    $this->Evaluation->Event->saveField('average_rating', $this->request->data['Evaluation']['rating']);
                } else if ($jaAvaliou) {
                    $evaluations = $this->Evaluation->find('all', array('recursive' => -1, 'fields' => array('SUM(Evaluation.rating) as soma', 'COUNT(Evaluation.id) as qtd'), 'Evaluation.event_id' => $this->request->data['Evaluation']['event_id']));
                    $media = $evaluations[0][0]['soma'] / $evaluations[0][0]['qtd'];
                    $this->Evaluation->Event->id = $this->request->data['Evaluation']['event_id'];
                    $this->Evaluation->Event->saveField('average_rating', $media);
                } else {
                    $mediaBd = $this->Evaluation->Event->field('average_rating', array('Event.id' => $this->request->data['Evaluation']['event_id']));
                    $this->Evaluation->Event->id = $this->request->data['Evaluation']['event_id'];
                    $media = ($mediaBd + $this->request->data['Evaluation']['rating']) / 2;
                    $this->Evaluation->Event->saveField('average_rating', $media);
                }
                return $response = json_encode(array(
                    'success' => true, 'data' => 'Obrigado por avaliar o evento.'));
            } else {
                return $response = json_encode(array('success' => false));
            }
        } else {
            return $response = json_encode(array('success' => false));
        }
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
