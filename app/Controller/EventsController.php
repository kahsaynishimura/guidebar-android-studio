<?php

App::uses('AppController', 'Controller');
App::uses('CakeEmail', 'Network/Email');

/**
 * Events Controller
 *
 * @property Event $Event
 * @property PaginatorComponent $Paginator
 */
class EventsController extends AppController {

    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator', 'Uploads', 'RequestHandler', 'Mpdf');
    public $helpers = array('GoogleMap', 'Html');
    public $paginate = array('Event' => array(
            'limit' => 25,
            'fields' => array('Event.id', 'Event.created', 'Event.name', 'Event.thumb',
                'Event.user_id', 'Event.start_date', 'Event.end_date',
                'Event.category_id', 'Event.is_open_bar', 'Event.is_active',
                'Event.description', 'Event.views'),
            'contain' => array(
                'Category' => array('fields' => array('id', 'name')),
                'Product' => array('fields' => array('id'),
                    'Item' => array('fields' => array('quantity')))
            )
    ));

    public function beforeFilter() {
        parent::beforeFilter();
        if ($this->RequestHandler->ext === 'json') {
            $this->Auth->allow('getEventImages');
        }

        $this->Auth->allow(array('messagePromoter', 'facebookComments', 'myEventsForPaymentApp', 'getEventsForPaymentApp'));
    }

    /**
     * index method
     *
     * @return void
     */
    public function index() {
        $categoriesTemp = $this->Event->Category->find('list');
        $categories = array();
        array_push($categories, 'Todas');
        foreach ($categoriesTemp as $item) {
            array_push($categories, $item);
        }
        $this->loadModel('State');
        $states = $this->State->find('list');
        $this->loadModel('City');
        $cities = $this->City->find('list', array('conditions' => array('City.state_id' => 1)));
        $title_events = 'Resultados da busca';
        if ($this->request->query('srch-term') != '') {
            $conditions = array('Event.is_active' => true, 'Event.name LIKE' => "%" . $this->request->query('srch-term') . "%");
            $this->set('events', $this->Event->find('all', array('conditions' => $conditions)));
        } else {
            $conditions = array();
            if ($this->request->data != null) {
                if ($this->request->data['Event']['category_id'] != 0) {
                    $conditions[] = array('Event.category_id' => $this->request->data['Event']['category_id']);
                }
                if ($this->request->query('categoryFilter') == null) {
                    $start_date = $this->request->data['Event']['start_date'];
                    $s_date = date("Y-m-d H:i", strtotime($start_date['year'] . '-' . $start_date['month'] . '-' . $start_date['day'] . ' ' . $start_date['hour'] . ':' . $start_date['min']));
                    //$conditions[] = array('Event.start_date >=' => $s_date);
                    if ($this->request->data['Event']['is_open_bar'] == 1) {
                        $conditions[] = array('Event.is_open_bar' => true);
                    }
                    $conditions[] = array('Address.city_id' => $this->request->data['Event']['city_id']);
                    $cities = $this->City->find('list', array('conditions' => array('City.state_id' => $this->request->data['Event']['state_id'])));
                }
            } else {

                $title_events = 'Próximos eventos';
                $s_date = date("Y-m-d H:i", strtotime('now'));
                //$conditions[] = array('Event.start_date >=' => $s_date);
            }
            $conditions[] = array('Event.is_active' => true);
            if ($this->RequestHandler->ext == 'json') {
                $events = $this->Event->find('all', array('conditions' => $conditions, 'contain' => array('Address' => array('fields' => 'city_id', 'id'))));
            } else {
                $events = $this->Event->find('all', array('conditions' => $conditions));
            }
            $this->set('events', $events);
            if (count($events) == 0) {
                $title_events = 'Nenhum evento foi encontrado.';
            }
        }
        $this->set('title_events', $title_events);
        $this->set(compact('categories', 'cities', 'states'));
    }

    public function getEventsForPaymentApp() {
        $conditions = array();
        $conditions[] = array('Event.is_active' => true,'Event.start_date >='=>$this->request->data['Event']['start_date']);
        $events = $this->Event->find('all', array('conditions' => $conditions, 'contain' => array('Address' => array('fields' => 'latitude', 'longitude'))));
        $this->set('events', $events);
    }

    public function myEvents() {
        $categoriesTemp = $this->Event->Category->find('list');
        $categories = array();
        array_push($categories, 'Todas');
        foreach ($categoriesTemp as $item) {
            array_push($categories, $item);
        }
        $this->loadModel('State');
        $states = $this->State->find('list');
        $this->loadModel('City');
        $cities = $this->City->find('list', array('conditions' => array('City.state_id' => 1)));
        $title = 'Meus eventos';
        $conditions = array();
        if ($this->request->data != null && $this->RequestHandler->ext !== 'json') {
            $title = 'Resultados da busca';
            if ($this->request->data['Event']['category_id'] != 0) {
                $conditions[] = array('Event.category_id' => $this->request->data['Event']['category_id']);
            }
            $start_date = $this->request->data['Event']['start_date'];
            $s_date = date("Y-m-d H:i", strtotime($start_date['year'] . '-' . $start_date['month'] . '-' . $start_date['day'] . ' ' . $start_date['hour'] . ':' . $start_date['min']));
            $conditions[] = array('Event.start_date >=' => $s_date);
            if ($this->request->data['Event']['is_open_bar'] == 1) {
                $conditions[] = array('Event.is_open_bar' => true);
            }
            $conditions[] = array('Address.city_id' => $this->request->data['Event']['city_id']);
            $cities = $this->City->find('list', array('conditions' => array('City.state_id' => $this->request->data['Event']['state_id'])));
        } else {
            $s_date = date("Y-m-d H:i", strtotime('now'));
            // $conditions[] = array('Event.start_date >=' => $s_date);
        }
        if ($this->RequestHandler->ext === 'json') {
            $conditionsJSON = array('Event.user_id' => $this->Auth->user('id'));
            $events = $this->Event->find('all', array('conditions' => $conditionsJSON, 'recursive' => -1));
            $this->set('events', $events);
        } else {
            $conditions[] = array('Event.user_id' => $this->Auth->user('id'));
            $this->Event->Behaviors->load('Containable');
//            $events = $this->Event->find('all', array('conditions' => $conditions));
//            $this->log($this->paginate);
//            $this->paginate['Event']
//                    ['conditions']=$conditions;
            $events = $this->Paginator->paginate('Event', $conditions);


            $this->set('title', $title);
            $this->set('events', $events);
            $this->set(compact('categories', 'cities', 'states'));
        }
    }

    public function myEventsForPaymentApp() {

        $user = $this->User->find('first', array('conditions' => array('User.email' => $this->request->data['User']['email'])));

        if (!empty($user['User']) && $this->RequestHandler->ext === 'json' && $this->request->is('post')) {
            $this->Event->Behaviors->load('Containable');
            $conditionsJSON = array('Event.user_id' => $user['User']['id']);
            $events = $this->Event->find('all', array('conditions' => $conditionsJSON));
            $this->set('events', $events);
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
        if (!$this->Event->exists($id)) {
            throw new NotFoundException(__('Invalid event'));
        }
        $this->Event->Behaviors->load('Containable');
        $options = array(
            'conditions' => array('Event.' . $this->Event->primaryKey => $id),
            'contain' => array(
                'Product' => array(
                    'fields' => array('id'),
                    'conditions' => array('Product.product_type_id' => '2', 'Product.is_active' => '1')
                ),
                'Evaluation' => array(
                    'User' => array('fields' => array('id')),
                    'fields' => array('rating', 'user_id'),
                    'conditions' => array('user_id' => $this->Auth->user('id'))
                ),
                'Attendance' => array(
                    'User' => array('fields' => array('name', 'id', 'thumb', 'filename')),
                    'conditions' => array('user_id' => $this->Auth->user('id'))
                ),
                'Bookmark' => array(
                    'User' => array('fields' => array('name', 'id')),
                    'conditions' => array('user_id' => $this->Auth->user('id'))
                ),
                'User' => array('fields' => array('name', 'id', 'email', 'is_selling_ticket_pagseguro', 'is_selling_ticket_moip', 'email_moip')),
                'Category' => array('fields' => array('name', 'id')),
                'Address' => array(
                    'fields' => array('id', 'street', 'place_name', 'street_number', 'complement', 'neighborhood', 'zip_code', 'latitude', 'longitude'),
                    'City' => array(
                        'fields' => array('id', 'name'),
                        'State' => array('fields' => array('id', 'name'))
                    )
                ),
                'Sponsor' => array(
                    'fields' => array('id', 'name', 'url', 'filename')
                )
            )
        );

        $event = $this->Event->find('first', $options);
        if ($event['Event']['is_active'] == 0 && $this->Auth->user('id') != $event['User']['id']) {
            $this->Session->setFlash("O evento que você tentou acessar foi desabilitado pelo promotor");
            return $this->redirect($this->referer());
        }
        $this->Event->Attendance->Behaviors->load('Containable');
        $event['Attendees'] = $this->Event->Attendance->find('all', array(
            'conditions' => array('Attendance.event_id' => $id),
            'contain' => array(
                'User' => array('fields' => array('name', 'id', 'thumb', 'filename'))
            )
        ));
        if ($event['Event']['is_active']) {
            $this->Event->updateViews($event);
        }
        $this->set('event', $event);
        $attendance_action_text = 'Participar';
        $attendance_action = 'add';
        $bookmark_action_text = '<span class="glyphicon glyphicon-star-empty"></span> Marcar como favorito';
        $bookmark_action = 'add';
        foreach ($event['Attendance'] as $attendance) {
            if ($attendance['user_id'] === $this->Auth->user('id')) {

                $attendance_action = 'delete/' . $attendance['id'];
                $attendance_action_text = 'Cancelar participação';
            }
        }
        $this->set('attendance_action_text', $attendance_action_text);
        $this->set('attendance_action', $attendance_action);
        foreach ($event['Bookmark'] as $bookmark) {
            if ($bookmark['user_id'] === $this->Auth->user('id')) {

                $bookmark_action = 'delete/' . $bookmark['id'];
                $bookmark_action_text = '<span class="glyphicon glyphicon-star"></span> Remover dos favoritos';
            }
        }
        $this->set('bookmark_action_text', $bookmark_action_text);
        $this->set('bookmark_action', $bookmark_action);
        $this->set('products', $this->Event->Product->find('all', array(
                    'conditions' => array('Product.event_id' => $id, 'Product.product_type_id !=' => '1', 'Product.is_active' => 1), //array of conditions
                    'recursive' => 0, //int
                    'fields' => array('id', 'product_type_id', 'is_active', 'description', 'name', 'price', 'quantity', 'quantity_available', 'event_id'),
                    'order' => array('Product.description' => 'asc'),
                    'callbacks' => true //other possible values are false, 'before', 'after'
        )));
        $evaluation_rating = 0;

        foreach ($event['Evaluation'] as $evaluation) {

            if ($evaluation['user_id'] === $this->Auth->user('id')) {
                $evaluation_rating = $evaluation['rating'];
            }
        }
        $this->set('evaluation_rating', $evaluation_rating);
    }

    /**
     * add method
     *
     * @return void
     */
    public function add() {
        $state_id = '1';

        if ($this->request->is('post')) {

            $new_data = $this->request->data['Event'];
            $this->Event->create();
            $new_data['user_id'] = $this->Auth->user('id');
            $new_data['Address']['user_id'] = $this->Auth->user('id');
            $image = $new_data['filename'];
            $new_data['filename'] = 'icone_transparente.png';
            $new_data['thumb'] = 'icone_transparente.png';
            if ($new_data['Tickets']) {
                $tickets = json_decode($new_data['Tickets'], true);
                $new_data['Product'] = array();
                foreach ($tickets as $ticket) {
                    $ticket['Product']['quantity_available'] = $ticket['Product']['quantity'];
                    $ticket['Product']['product_type_id'] = 2;
                    array_push($new_data['Product'], $ticket['Product']);
                }
            }
            unset($new_data['Tickets']);
            if ($new_data['address_id'] > 0) {
                unset($new_data['Address']);
            } else {
                unset($new_data['address_id']);
            }
            if ($this->Event->saveAssociated($new_data, array('deep' => true))) {

                $new_data['id'] = $id = $this->Event->getLastInsertId();
                if (!empty($image['name'])) {

                    $upload = $this->Uploads->upload($image, 'img/Eventos/' . $id, "EVENT_" . $id . "." . pathinfo($image['name'], PATHINFO_EXTENSION));
                    $new_data['filename'] = str_replace("img/", "", $upload);
                    $new_data['thumb'] = str_replace("img/", "", "Eventos/" . $id . "/thumb/" . "EVENT_" . $id . "." . pathinfo($image['name'], PATHINFO_EXTENSION));
                }
                $this->Event->save($new_data);
                return $this->redirect(array('controller' => 'events', 'action' => 'manage', $new_data['id']));
            } else {
                $this->set('data[Event]', $this->request->data['Event']);
                $this->Session->setFlash(__('O evento não foi salvo. Por favor, verifique os dados e tente novamente.'));
            }
        }

        $promoter = $this->Event->User->find('first', array('conditions' => array('User.id' => $this->Auth->user('id'))));
        $this->set('promoter', $promoter);
        $categories = $this->Event->Category->find('list');

        $this->loadModel('State');
        $states = $this->State->find('list');
        $cities = $this->Event->Address->City->find('list', array(
            'conditions' => array('City.state_id' => $state_id),
            'recursive' => -1
                )
        );
        $addressesTemp = $this->Event->Address->find('list', array(
            'conditions' => array('Address.user_id' => $this->Auth->user('id'))
                )
        );
        $addresses[0] = __("Cadastre um novo endereço");
        foreach ($addressesTemp as $key => $address) {
            $addresses[$key] = $address;
        }
        $this->set(compact('cities', 'states', 'categories', 'addresses'));
    }

    /**
     * edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function edit($id = null) {
        if (!$this->Event->exists($id)) {
            throw new NotFoundException(__('Invalid event'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            $new_data = $this->request->data['Event'];

            $new_data['Address']['user_id'] = $this->Auth->user('id');
            if ($new_data['address_id'] === "0") {
                $this->Event->Address->create();
                $this->Event->Address->save($new_data['Address']);
                $new_data['address_id'] = $this->Event->Address->getLastInsertID();

                unset($new_data['Address']);
            }

            if ($new_data['Tickets']) {
                $tickets = json_decode($new_data['Tickets'], true);
                $new_data['Product'] = array();
                foreach ($tickets as $ticket) {

                    if (!isset($ticket['id'])) {
                        $ticket['Product']['event_id'] = $new_data['id'];
                        $ticket['Product']['product_type_id'] = 2;
                        $ticket['Product']['quantity_available'] = $ticket['Product']['quantity'];
                        array_push($new_data['Product'], $ticket['Product']);
                    }
                }
                $this->Event->Product->saveMany($new_data['Product']);
            }
            unset($new_data['Tickets']);

            $fieldList = array('category_id', 'address_id', 'minimum_age', 'name', 'description', 'start_date', 'end_date', 'is_active', 'is_open_bar');
            $image = $new_data['filename'];
            if ($new_data['is_remove_image']) {
                $new_data['filename'] = "icone_transparente.png";
                $new_data['thumb'] = "icone_transparente.png";
                array_push($fieldList, 'filename');
                array_push($fieldList, 'thumb');
            } else if (!empty($image['name'])) {
                $upload = $this->Uploads->upload($new_data['filename'], 'img/Eventos/' . $id, "EVENT_" . $id . "." . pathinfo($image['name'], PATHINFO_EXTENSION));
                $new_data['filename'] = str_replace("img/", "", $upload);
                $new_data['thumb'] = str_replace("img/", "", "Eventos/" . $id . "/thumb/" . "EVENT_" . $id . "." . pathinfo($image['name'], PATHINFO_EXTENSION));
                array_push($fieldList, 'filename');
                array_push($fieldList, 'thumb');
            }

            if ($this->Event->save($new_data, true, $fieldList)) {
                return $this->redirect(array('controller' => 'events', 'action' => 'manage', $id));
            } else {
                $this->Session->setFlash(__('O evento não foi salvo. Por favor, tente novamente.'));
            }
        }
        $this->Event->Behaviors->load('Containable');
        $options = array('conditions' => array('Event.' . $this->Event->primaryKey => $id),
//                    'fields'=>array(),
            'contain' => array(
                'Address' => array(
                    'fields' => array('place_name', 'street', 'street_number', 'complement', 'neighborhood', 'zip_code', 'city_id', 'latitude', 'longitude'),
                    'City' => array('fields' => array('state_id'))
                ),
                'Product' => array(
                    'fields' => array('id', 'name', 'price', 'quantity', 'is_active', 'event_id'),
                    'Item' => array('fields' => array('id'))
                )
        ));

        $this->request->data = $this->Event->find('first', $options);
        $products = array();
        foreach ($this->request->data['Product'] as $key => $product) {
            array_push($products, array("Product" => $product));
        }
        $this->request->data['Event']['Tickets'] = json_encode($products);
        $this->request->data['Event']['Address'] = $this->request->data['Address'];
        unset($this->request->data['Product']);
        unset($this->request->data['Address']);
        $categories = $this->Event->Category->find('list');
        $promoter = $this->Event->User->find('first', array('recursive' => 0, 'conditions' => array('User.id' => $this->Auth->user('id'))));
        $this->set('promoter', $promoter);
        $this->loadModel('State');
        $states = $this->State->find('list');
        $cities = $this->Event->Address->City->find('list', array(
            'conditions' => array('City.state_id' => 1),
            'recursive' => -1
                )
        );
        $addressesTemp = $this->Event->Address->find('list', array(
            'conditions' => array('Address.user_id' => $this->Auth->user('id'))
                )
        );
        $addresses[0] = __("Cadastre um novo endereço");
        foreach ($addressesTemp as $key => $address) {
            $addresses[$key] = $address;
        }
        $this->set(compact('cities', 'states', 'categories', 'addresses'));
    }

    /**
     * manage method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    function manage($id = null) {

        if (!$this->Event->exists($id)) {
            throw new NotFoundException(__('Invalid event'));
        }
        $this->Event->Behaviors->load('Containable');
        $options = array(
            'conditions' => array('Event.' . $this->Event->primaryKey => $id),
            'contain' => array(
                'Complaint' => array(
                    'User' => array('fields' => array('name', 'id'))
                ),
                'Attendance' => array(
                    'User' => array('fields' => array('name', 'id', 'email'))
                ),
                'Bookmark' => array('User' => array('fields' => array('name', 'id'))),
                'User' => array('fields' => array('name', 'id', 'email', 'email_pagseguro', 'email_moip', 'token_pagseguro', 'is_selling_ticket_pagseguro', 'is_selling_ticket_moip')),
                'Category' => array('fields' => array('name', 'id')),
                'Address' => array(
                    'fields' => array('id', 'place_name', 'street', 'street_number', 'complement', 'neighborhood', 'zip_code', 'latitude', 'longitude'),
                    'City' => array(
                        'fields' => array('id', 'name'),
                        'State' => array('fields' => array('id', 'name'))
                    )
                ),
                'Product' => array(
                    'fields' => array('id', 'name', 'price', 'quantity', 'is_active', 'event_id', 'quantity_available'),
                    'conditions' => array('Product.product_type_id' => '2'),
                    'Item' => array('fields' => array('id', 'quantity'))
                ),
                'Sponsor' => array(
                    'fields' => array('id', 'name', 'url')
                )
            )
        );

        $event = $this->Event->find('first', $options);
        $products = array();
        foreach ($event['Product'] as $product) {
            array_push($products, array('Product' => $product));
        }
        $event['Event']['Tickets'] = json_encode($products);

        $sponsors = array();
        foreach ($event['Sponsor'] as $sponsor) {
            array_push($sponsors, array('Sponsor' => $sponsor));
        }
        $event['Event']['Sponsors'] = json_encode($sponsors);

        $this->Event->Product->Behaviors->load('Containable');
        $options = array(
            'conditions' => array('Product.event_id' => $event['Event']['id'], 'Product.product_type_id' => '1'),
            'contain' => array(
                'Item' => array(
                    'fields' => array('validated')
                )
            ),
            'fields' => array('id', 'product_type_id', 'name', 'quantity', 'price', 'created'),
        );
        $event['previous_tickets'] = $this->Event->Product->find('all', $options);

        $this->set('event', $event);
        $bookmarksCount = $this->Event->Bookmark->find("count", array('conditions' => array('Bookmark.event_id' => $id)));
        $evaluationsCount = $this->Event->Evaluation->find("count", array('conditions' => array('Evaluation.event_id' => $id)));
        $this->set('bookmarksCount', $bookmarksCount);
        $this->set('evaluationsCount', $evaluationsCount);
    }

    public function delete($id = null) {
        $this->Event->id = $id;
        if (!$this->Event->exists()) {
            throw new NotFoundException(__('Invalid event'));
        }
        if ($this->Event->delete()) {
            echo $this->Upload->deleteAll('Event', $id);
            $this->Session->setFlash(__('O evento foi apagado.'));
        } else {
            $this->Session->setFlash(__('O evento não pôde ser apagado. Por favor, tente novamente mais tarde.'));
        }
        $this->redirect(array('controller' => 'events', 'action' => 'myEvents'));
    }

    public function getEventImages() {
        $thumbs = glob(WWW_ROOT . "files/Event/" . $this->request->data['id'] . "/*.*");
        $images = array();
        if (count($thumbs)) {
            natcasesort($thumbs);
            foreach ($thumbs as $thumb) {
                array_push($images, substr($thumb, strrpos($thumb, 'files/Event')));
            }
        }
        $this->set('images', $images);
    }

    public function buscaEndereco($id = null) {
        if (!$this->Event->exists($id)) {
            throw new NotFoundException(__('Endereço inválido'));
        }
        $options = array('conditions' => array('Event.' . $this->Event->primaryKey => $id));
        $event = $this->Event->find('first', $options);
        $addressId = $event['Address']['id'];
        if (isset($addressId)) {
            $this->redirect(array('controller' => 'addresses', 'action' => 'edit', $addressId));
        } else {
            $this->redirect(array('controller' => 'addresses', 'action' => 'add', $id));
        }
    }

    public function deactivate($id = null) {
        $this->autoRender = false;
        $this->Event->id = $id;
        if (!$this->Event->exists()) {
            $this->Session->setFlash('O evento que você tentou desativar não existe.');
            return $response = json_encode(array('success' => false));
        } else {
            if ($this->Event->saveField('is_active', false)) {
                if ($this->RequestHandler->ext === 'json') {
                    $this->autoRender = false;
                    $this->set('response', json_encode(array('success' => true)));
                    return $response = json_encode(array('success' => true));
                } else {
                    $this->redirect($this->referer());
                }
            }
        }
    }

    public function activate($id = null) {
        $this->Event->id = $id;
        if (!$this->Event->exists()) {
            $this->Session->setFlash('O evento que você tentou ativar não existe.');
        } else {
            if ($this->Event->saveField('is_active', true)) {
                if ($this->RequestHandler->ext === 'json') {
                    $this->autoRender = false;
                    $this->set('response', json_encode(array('success' => true, 'errors' => '')));
                    return $response = json_encode(array('success' => true, 'errors' => ''));
                } else {
                    $this->redirect($this->referer());
                }
            }
        } if ($this->RequestHandler->ext === 'json') {
            $this->autoRender = false;
            $response = json_encode(array('success' => true, 'errors' => ''));
            return $response;
        } else {
            $this->redirect($this->referer());
        }
    }

    public function uploadImage($id = null) {
        $this->set('eventId', $id);
        if (!$this->Event->exists($id)) {
            throw new NotFoundException(__('Invalid event'));
        }
    }

    function printAttendance($eventId) {
        if (!$this->Event->exists($eventId)) {
            throw new NotFoundException(__('Invalid event'));
        }

        $this->Event->Attendance->Behaviors->load('Containable');
        $attendances = $this->Event->Attendance->find('all', array(
            'conditions' => array('Attendance.event_id' => $eventId),
            'contain' => array(
                'User' => array(
                    'fields' => array('name', 'email')
                )
            )
        ));
        $this->set('attendances', $attendances);


        if ($this->RequestHandler->ext === 'pdf') {
            // initializing mPDF
            $this->Mpdf->init();
        }
    }

    public function messagePromoter() {
        $this->autoRender = FALSE;
        $this->autoLayout = FALSE;
        $Email = new CakeEmail('smtp');

        $Email->from(array('thiago@guidebar.com.br' => 'Guidebar'))
                ->to($this->request->data['EmailData']['promoter_id'])
                ->subject('Guidebar - Nova mensagem do evento ' . $this->request->data['EmailData']['event_name'] . '.')
                ->template('talk_to_promoter', 'default')
                ->emailFormat('html')
                ->viewVars(array('mensagem' => $this->request->data['EmailData']['mensagem'], 'email' => $this->request->data['EmailData']['email'], 'name' => $this->request->data['EmailData']['nome']))
                ->send();

        return json_encode(array('success' => true));
    }

    public function facebookComments($eventId) {
        $this->layout = 'empty';
        $event = array('Event' => array('id' => $eventId));
        $this->set('event', $event);
    }

    public function isAuthorized($user) {
        if (!parent::isAuthorized($user)) {
            if (in_array($this->action, array('add', 'myEvents'))) {
                // Todos os usuários logados podem criar eventos
                return true;
            }
            if (in_array($this->action, array('edit', 'manage', 'printAttendance', 'delete', 'buscaEndereco', 'uploadImage', 'deactivate', 'activate'))) {
                $eventId = $this->request->params['pass'][0];
                return $this->Event->isOwnedBy($eventId, $user['id']);
            }
        }
        return false;
    }

}
