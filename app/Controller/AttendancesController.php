<?php

App::uses('AppController', 'Controller');
App::uses('CakeEmail', 'Network/Email');

/**
 * Attendances Controller
 *
 * @property Attendance $Attendance
 * @property PaginatorComponent $Paginator
 */
class AttendancesController extends AppController {

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
        $this->autoLayout = false;
        $this->layout = 'ajax';
        if (($this->request->is('post') && $this->request->is('ajax') && !empty($this->data)) || ($this->RequestHandler->ext === 'json')) {
            $this->Attendance->create();
            $this->request->data['Attendance']['user_id'] = $this->Auth->user('id');
            if ($this->Attendance->save($this->request->data)) {
                $event_id = $this->request->data['Attendance']['event_id'];
                $options = array(
                    'conditions' => array('Attendance.event_id' => $event_id), //array of conditions
                    'recursive' => 1, //int
                    //string or array defining order
                    'order' => array('Attendance.created' => 'desc'),
                    'callbacks' => true //other possible values are false, 'before', 'after'
                );
//                $Email = new CakeEmail('smtp');
//                $Email->from(array('thiago@guidebar.com.br' => 'Guidebar'))
//                        ->to(array('karina_nishimura@hotmail.com', 'kahsaynishimura@gmail.com', 'thiagocomar@gmail.com'))//, 'thiagocomar@gmail.com'
//                        ->subject('Guidebar - Confirmação de cadastro')
//                        ->template('confirmation', 'default')
//                        ->emailFormat('html')
//                        ->viewVars(array('email' => 'karina.n.comar@ddddd', 'key' => 'key', 'userName' => 'Thiago Comar'))
//                        ->send();
                $this->set('response', json_encode(array(
                    'success' => true, 
                    'id' => $this->Attendance->getLastInsertID())));
                $this->set('attendances', $this->Attendance->find('all', $options));
                $this->render('list_attendances', 'ajax');
            } else {
                return $response = json_encode(array('success' => false, 'id' => 0));
            }
        } else {
            return $response = json_encode(array('success' => false, 'id' => 0));
        } 
    }

    /**
     * delete method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function delete($id = null) {

        $this->autoRender = false;
        $this->Attendance->id = $id;

        if (!$this->Attendance->exists()) {
            $response = json_encode(array('success' => false));
            $attendances = array();
            $this->set('attendances', $attendances);
            $this->set('response', $response);
            return $response;
        }
        $options = array(
            'conditions' => array('Attendance.id' => $id),
            'recursive' => 1,
            'order' => array('Attendance.created' => 'desc'),
            'callbacks' => true
        );
        $attendance = $this->Attendance->find('first', $options);
        $event_id = $attendance['Attendance']['event_id'];
        if ($this->Attendance->delete()) {
            $options = array(
                'conditions' => array('Attendance.event_id' => $event_id),
                'recursive' => 1,
                'order' => array('Attendance.created' => 'desc'),
                'callbacks' => true
            );
            $response = json_encode(array('success' => true));
            $attendances = $this->Attendance->find('all', $options);
            $this->set('attendances', $attendances);
            $this->set('response', $response);
            $this->render('list_attendances', 'ajax');
        } else {
            $response = json_encode(array('success' => false));
            $attendances = array();
            $this->set('attendances', $attendances);
            $this->set('response', $response);
            return $response;
        }
    }

    public function isAuthorized($user) {
        if (!parent::isAuthorized($user)) {
            if ($this->action === 'add') {
                return true;
            }
            if (in_array($this->action, array('delete'))) {
                $attendanceId = $this->request->params['pass'][0];
                return $this->Attendance->isOwnedBy($attendanceId, $user['id']);
            }
        }
        return false;
    }

}
