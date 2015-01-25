<?php

App::uses('AppController', 'Controller');

/**
 * Products Controller
 *
 * @property Product $Product
 * @property PaginatorComponent $Paginator
 */
class ProductsController extends AppController {

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
    public function index($id = NULL) {
        if ($id == NULL) {
            $this->redirect(array('controller' => 'events'));
        } else {
            $event = $this->Product->Event->find('first', array('recursive' => 0, 'conditions' => array('Event.id' => $id)));
            $conditions = array();
            if (!($event['Event']['user_id'] == $this->Auth->user('id'))) {
                $conditions[] = array('Product.is_active' => 1);
            }
            if ($id) {
                $conditions['Product.event_id'] = $id;
                $this->set('eventId', $id);
            }
            $conditions['Product.product_type_id'] = '2';
            $this->Product->recursive = 0;
            $this->set('products', $this->Paginator->paginate($conditions));
        }
    }

    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($id = null) {
        if (!$this->Product->exists($id)) {
            throw new NotFoundException(__('Invalid product'));
        }
        $options = array('conditions' => array('Product.' . $this->Product->primaryKey => $id));
        $this->set('product', $this->Product->find('first', $options));
    }

    /**
     * add method
     *
     * @return void
     */
    public function add() {
        if (($this->Auth->user('token_pagseguro') == '' ||
                $this->Auth->user('token_pagseguro') == 'null' ||
                $this->Auth->user('token_pagseguro') == NULL) ||
                (
                $this->Auth->user('email_pagseguro') == '' ||
                $this->Auth->user('email_pagseguro') == 'null' ||
                $this->Auth->user('email_pagseguro') == NULL
                )) {
            $this->Session->setFlash('Para vender produtos é preciso informar os dados do pagseguro.');
            $this->redirect(array('controller' => 'users', 'action' => 'edit', $this->Auth->user('id')));
        }
        if ($this->request->is('post')) {
            $this->Product->create();
            $image = $this->request->data['Product']['image'];

            $this->request->data['Product']['product_type_id'] = '3';
            $this->request->data['Product']['image'] = 'Produtos/default.jpg';
            $this->request->data['Product']['event_id'] = $this->request->params['pass'][0];
            $this->request->data['Product']['quantity_available']=$this->request->data['Product']['quantity'];
            if ($this->Product->save($this->request->data)) {
                $this->Session->setFlash(__('O produto foi adicionado.'));
                $id = $this->Product->getLastInsertID();
                $this->request->data['Product']['id'] = $id;
                if (!empty($image['name'])) {
                    $upload = $this->Uploads->upload($image, 'img/Produtos/' . $id);
                    $this->request->data['Product']['image'] = str_replace("img/", "", $upload);
                }
                $this->Product->save($this->request->data);
                return $this->redirect(array('controller' => 'products', 'action' => 'index', $this->request->params['pass'][0]));
            } else {
                $this->Session->setFlash(__('Não foi possível adicionar o produto. Por favor, tente novamente.'));
            }
        }
    }

    public function addTicket() {
        $this->autoLayout = false;
        $this->autoRender = false;
        if ($this->request->is('post')) {//&& $this->request->is('ajax')
            $errors = "";
            if (($this->Auth->user('token_pagseguro') == '' ||
                    $this->Auth->user('token_pagseguro') == 'null' ||
                    $this->Auth->user('token_pagseguro') == NULL) ||
                    (
                    $this->Auth->user('email_pagseguro') == '' ||
                    $this->Auth->user('email_pagseguro') == 'null' ||
                    $this->Auth->user('email_pagseguro') == NULL
                    )) {
                $errors .= ('Para vender produtos é preciso informar os dados do pagseguro.');
            }

            $this->Product->create();

            $this->request->data['Product']['product_type_id'] = '2';
            $this->request->data['Product']['event_id'] = $this->request->params['pass'][0];
            if ($this->Product->save($this->request->data)) {
                $id = $this->Product->getLastInsertID();
                $this->request->data['Product']['id'] = $id;

                return json_encode(array('success' => TRUE, 'id' => $id));
            } else {

                return json_encode(array('success' => FALSE, 'errors' => $errors));
            }
        } else {
            $this->Session->setFlash(__('Não foi possível adicionar o ingresso. Por favor, tente novamente.'));
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
        if (($this->Auth->user('token_pagseguro') == '' ||
                $this->Auth->user('token_pagseguro') == 'null' ||
                $this->Auth->user('token_pagseguro') == NULL) ||
                (
                $this->Auth->user('email_pagseguro') == '' ||
                $this->Auth->user('email_pagseguro') == 'null' ||
                $this->Auth->user('email_pagseguro') == NULL
                )) {
            $this->Session->setFlash('Para vender produtos é preciso informar os dados do pagseguro.');
            $this->redirect(array('controller' => 'users', 'action' => 'edit', $this->Auth->user('id')));
        }
        if (!$this->Product->exists($id)) {
            throw new NotFoundException(__('Invalid product'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            $fieldlist = array('name', 'description', 'price', 'quantity', 'is_active');
            $image = $this->request->data['Product']['image'];
            if (!empty($image['name'])) {
                $upload = $this->Uploads->upload($this->request->data['Product']['image'], 'img/Produtos/' . $id);

                $this->request->data['Product']['image'] = str_replace("img/", "", $upload);
                array_push($fieldlist, 'image');
            }
            if ($this->Product->save($this->request->data, true, $fieldlist)) {
                $this->Session->setFlash(__('O produto foi salvo.'));
                return $this->redirect(array('controller' => 'products', 'action' => 'view', $id));
            } else {
                $this->Session->setFlash(__('Não foi possível salvar o produto. Por favor, tente novamente.'));
            }
        } else {
            $options = array('conditions' => array('Product.' . $this->Product->primaryKey => $id));
            $this->request->data = $this->Product->find('first', $options);
        }
    }

    public function delete($id = null) {
        $this->autoLayout = FALSE;
        $this->autoRender = FALSE;
        $this->Product->id = $id;
        if (!$this->Product->exists()) {
            throw new NotFoundException(__('Invalid product'));
        }
        if ($this->request->is('ajax')) {
            if ($this->Product->delete()) {
                return json_encode(array('success' => TRUE));
            } else {
                return json_encode(array('success' => FALSE));
            }
        }
    }

    public function setIsActive($id) {
        $this->autoRender = false;
        $this->autoLayout = false;
        $this->Product->id = $id;
        if (!$this->Product->exists()) {
            return json_encode(array('success' => FALSE, 'errors' => __('O ingresso que você tentou ativar é inválido.')));
        } else {
            $new_value = ($this->request->data['updatevalue'] == "true") ? 1 : 0;
            if ($this->Product->saveField('is_active', $new_value)) {
                return json_encode(array('success' => TRUE));
            } else {
                return json_encode(array('success' => FALSE, 'errors' => __("Não foi possível alterar o ingresso")));
            }
        }
    }

    public function isAuthorized($user) {
        if (!parent::isAuthorized($user)) {
            if (in_array($this->action, array('edit', 'setIsActive', 'delete'))) {
                $productId = $this->request->params['pass'][0];
                $options = array('conditions' => array('Product.' . $this->Product->primaryKey => $productId));
                $product = $this->Product->find('first', $options);
                return $this->Product->Event->isOwnedBy($product['Event']['id'], $user['id']);
            }
            if (in_array($this->action, array('add', 'addTicket')) && isset($this->request->params['pass'][0])) {
                $eventId = $this->request->params['pass'][0];
                return $this->Product->Event->isOwnedBy($eventId, $user['id']);
            }
        }
        return FALSE;
    }

}
