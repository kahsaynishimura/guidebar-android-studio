<?php

App::uses('AppController', 'Controller');

/**
 * Sponsors Controller
 *
 * @property Sponsor $Sponsor
 * @property PaginatorComponent $Paginator
 */
class SponsorsController extends AppController {

    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator', 'Uploads');

    /**
     * index method
     *
     * @return void
     */
    public function index() {
        $this->Sponsor->recursive = 0;
        $this->set('sponsors', $this->Paginator->paginate());
    }

    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($id = null) {
        if (!$this->Sponsor->exists($id)) {
            throw new NotFoundException(__('Invalid sponsor'));
        }
        $options = array('conditions' => array('Sponsor.' . $this->Sponsor->primaryKey => $id));
        $this->set('sponsor', $this->Sponsor->find('first', $options));
    }

    /**
     * add method
     *
     * @return void
     */
    public function add() {
        $this->autoLayout = FALSE;
        $this->autoRender = FALSE;
        if ($this->request->is('post')) {
            $this->Sponsor->create();
            if ($this->Sponsor->save($this->request->data['Sponsor'])) {
                $id=  $this->Sponsor->getLastInsertID();
                $image = isset($_FILES['image']) ? $_FILES['image'] : "";
                if (!empty($image['name']) && $this->request->data['Sponsor']['include_image'] == "1") {
                    $upload = $this->Uploads->upload($image, 'img/Patrocinadores/' . $id, "SPONSOR_" . $id . "." . pathinfo($image['name'], PATHINFO_EXTENSION));
                    $this->Sponsor->id = $this->Sponsor->getLastInsertID();
                    $this->Sponsor->saveField('filename', str_replace("img/", "", $upload));
                }
                $this->request->data = array();
                return json_encode(array('success' => TRUE, 'id' => $id));
            } else {
                return json_encode(array('success' => FALSE));
            }
        }
        return json_encode(array('success' => FALSE));
    }

    /**
     * edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function edit($id = null) {
        if (!$this->Sponsor->exists($id)) {
            throw new NotFoundException(__('Invalid sponsor'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Sponsor->save($this->request->data)) {
                return $this->flash(__('The sponsor has been saved.'), array('action' => 'index'));
            }
        } else {
            $options = array('conditions' => array('Sponsor.' . $this->Sponsor->primaryKey => $id));
            $this->request->data = $this->Sponsor->find('first', $options);
        }
        $events = $this->Sponsor->Event->find('list');
        $this->set(compact('events'));
    }

    /**
     * delete method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function delete($id = null) {
        $this->autoLayout = FALSE;
        $this->autoRender = FALSE;
        $this->Sponsor->id = $id;
        if (!$this->Sponsor->exists()) {
            throw new NotFoundException(__('Invalid sponsor'));
        }
        
            echo $this->Upload->deleteAll('Sponsor', $id);
        if ($this->request->is('ajax')) {
            if ($this->Sponsor->delete()) {
                return json_encode(array('success' => TRUE));
            } else {
                return json_encode(array('success' => FALSE));
            }
        }
    }

    public function isAuthorized($user) {
        if (!parent::isAuthorized($user)) {
            if (in_array($this->action, array('delete'))) {
                $id = $this->request->params['pass'][0];
                $options = array('conditions' => array('Sponsor.' . $this->Sponsor->primaryKey => $id));
                $sponsor = $this->Sponsor->find('first', $options);
                return $this->Sponsor->Event->isOwnedBy($sponsor['Event']['id'], $user['id']);
            }
            if (in_array($this->action, array('add'))) {
                $eventId = $this->request->data['Sponsor']['event_id'];
                return $this->Sponsor->Event->isOwnedBy($eventId, $user['id']);
            }
        }
        return FALSE;
    }

}
