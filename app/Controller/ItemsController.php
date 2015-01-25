<?php

App::uses('AppController', 'Controller');

/**
 * Items Controller
 *
 * @property Item $Item
 * @property PaginatorComponent $Paginator
 */
class ItemsController extends AppController {

    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator', 'PagSeguro.Carrinho', 'RequestHandler', 'Mpdf');

    /**
     * index method
     *
     * @return void
     */
    public function index() {
        $this->Item->recursive = 0;
        $this->set('items', $this->Paginator->paginate());
    }

    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($id = null) {
        if (!$this->Item->exists($id)) {
            throw new NotFoundException(__('Invalid item'));
        }
        $options = array('conditions' => array('Item.' . $this->Item->primaryKey => $id));
        $this->set('item', $this->Item->find('first', $options));
    }

    /**
     * add method
     *
     * @return void
     */
    public function addItems() {
        $this->autoLayout = FALSE;
        $this->autoRender = FALSE;
        if ($this->request->is('post') && $this->RequestHandler->ext === 'json') {
            $lst = json_decode($this->request->data["items"]);
            $this->Item->Purchase->Behaviors->load('Containable');

            $compra['Purchase']['sessionid'] = $this->Session->id();
            $compra['Purchase']['user_id'] = $this->Auth->user('id');
            $compra['Purchase']['event_id'] = $lst[0]->idEvento;
            $this->Item->Purchase->create();
            $this->Item->Purchase->save($compra, false);
            $compra['Purchase']['id'] = $this->Item->Purchase->getLastInsertId();
            $compra['Purchase']['code'] = AuthComponent::password($compra['Purchase']['id'] . $this->Auth->user('email'));
            $this->Item->Purchase->saveField('code', $compra['Purchase']['code']);

            $total = 0;
            foreach ($lst as $item) {
                $product = $this->Item->Product->find('first', array('conditions' => array('Product.id' => $item->id)));

                $this->request->data['Item']['purchase_id'] = $compra['Purchase']['id'];
                $this->Item->create();
                $this->request->data['Item']['subtotal'] = sprintf('%01.2f', $product['Product']['price'] * $item->quantity);
                $this->request->data['Item']['price'] = sprintf('%01.2f', $product['Product']['price']);
                $this->request->data['Item']['quantity'] = $item->quantity;
                $this->request->data['Item']['product_id'] = $item->id;
                $this->Item->save($this->request->data, array('validate' => true));
                $total+=$this->request->data['Item']['subtotal'];
            }
            $this->Item->Purchase->saveField('total', $total);

            $this->pagseguroApp($compra['Purchase']['id']);
        }
    }

    /**
     * add method
     *
     * @return void
     */
    public function add() {
        $this->autoLayout = FALSE;
        $this->autoRender = FALSE;
        if ($this->request->is('post') && $this->request->is('ajax')) {
            $product = $this->Item->Product->find('first', array('conditions' => array('Product.id' => $this->request->data['Item']['product_id'])));

            $this->Item->Purchase->Behaviors->load('Containable');
            $compra = $this->Item->Purchase->find('first', array('conditions' => array('Purchase.event_id' => $product['Product']['event_id'], 'Purchase.sessionid' => $this->Session->id())));
            if (empty($compra)) {
                $compra['Purchase']['sessionid'] = $this->Session->id();
                $compra['Purchase']['user_id'] = $this->Auth->user('id');
                $compra['Purchase']['event_id'] = $product['Product']['event_id'];
                $this->Item->Purchase->create();
                $this->Item->Purchase->save($compra, false);
                $compra['Purchase']['id'] = $this->Item->Purchase->getLastInsertId();
                $compra['Purchase']['code'] = AuthComponent::password($compra['Purchase']['id'] . $this->Auth->user('email'));
                $this->Item->Purchase->saveField('code', $compra['Purchase']['code']);
            }

            $this->request->data['Item']['purchase_id'] = $compra['Purchase']['id'];

            $existing = $this->Item->find('first', array(
                'conditions' => array(
                    'Item.product_id' => $this->request->data['Item']['product_id'],
                    'Item.purchase_id' => $compra['Purchase']['id']
            )));

            if ($existing) {
                $existing['Item']['quantity']+=$this->request->data['Item']['quantity'];
                $existing['Item']['subtotal'] = $existing['Item']['quantity'] * $existing['Item']['price'];
                $this->Item->save($existing);
            } else {

                $this->Item->create();
                $this->request->data['Item']['subtotal'] = sprintf('%01.2f', $product['Product']['price'] * $this->request->data['Item']['quantity']);
                $this->request->data['Item']['price'] = sprintf('%01.2f', $product['Product']['price']);
                $this->Item->save($this->request->data, array('validate' => true));
            }
            $this->set(compact('product'));
            return $this->render('add', 'ajax');
        } else {
            $this->redirect(array('controller' => 'events', 'action' => 'index'));
        }
    }

    function beforeFilter() {
        parent::beforeFilter();
        if ($this->request->is('ajax')) {
            $this->layout = 'ajax';
            if ($this->Auth->user() != NULL) {
                $this->Auth->allow('add');
            } else {
                return $response = json_encode(array('success' => false));
            }
        }
    }

    public function pagseguroApp($id) {
        $this->Item->Behaviors->load('Containable');
        $options = array(
            'conditions' => array('Purchase.id' => $id),
            'contain' => array(
                'Product' => array(
                    'Event' => array(
                        'User' => array(
                            'fields' => array('email_pagseguro', 'token_pagseguro')
                        )
                    )
                ),
                'Purchase'
            )
        );
        $items = $this->Item->find('all', $options);
        $this->Carrinho->setUrlRetorno('http://guidebar.com.br/purchases/finishPayment/' . $id);
        $this->Carrinho->setUrlNotificacao('http://guidebar.com.br/purchases/processNotification/' . $id);
        $this->Carrinho->setCredenciais(
                $items[0]['Product']['Event']['User']['email_pagseguro'], $items[0]['Product']['Event']['User']['token_pagseguro']);

        $total = 0;
        $order_item_count = 1;
        if (count($items) > 0) {
            foreach ($items as $item) {
                $productItem = $item['Product'];

                $this->Carrinho->adicionarItem($order_item_count, $productItem['name'], sprintf('%01.2f', $item['Item']['price']), 0, $item['Item']['quantity']);
                $this->Carrinho->setEmail($this->Auth->user('email'));

                $total += $item['Item']['subtotal'];
                $order_item_count++;
            }

            $shopData['total'] = sprintf('%01.2f', $total);
            $result = $this->Carrinho->finalizaCompra();

            if (strpos($result, 'UNAUTHORIZED') !== false) {
                $this->autoRender = FALSE;
                $this->autoLayout = FALSE;
                $this->layout = 'ajax';

                $this->Session->setFlash('Esta compra não está autorizada. Entre em contato com o vendedor.');
                $this->redirect($this->referer());
            } else {

                $this->Item->Purchase->id = $items[0]['Purchase']['id'];
                $this->Item->Purchase->saveField('total', sprintf('%01.2f', $total));

                $this->Item->Purchase->saveField('payment_url', $result);
                $this->Item->Purchase->saveField('sessionid', '');
                $response = array('success' => true, 'url' => $result);
            }
        } else {
            $shopData['total'] = 0;
            $response = array('success' => false, 'url' => '');
        }
        $this->set('response', $response);
        $this->render('add_items');
    }

    public function pagseguro($id) {
        $this->Item->Behaviors->load('Containable');
        $options = array(
            'conditions' => array('Purchase.id' => $id),
            'contain' => array(
                'Product' => array(
                    'Event' => array(
                        'User' => array(
                            'fields' => array('email_pagseguro', 'token_pagseguro')
                        )
                    )
                ),
                'Purchase'
            )
        );
        $items = $this->Item->find('all', $options);
        $this->Carrinho->setUrlRetorno('http://guidebar.com.br/purchases/finishPayment/' . $id);
        $this->Carrinho->setUrlNotificacao('http://guidebar.com.br/purchases/processNotification/' . $id);
        $this->Carrinho->setCredenciais(
                $items[0]['Product']['Event']['User']['email_pagseguro'], $items[0]['Product']['Event']['User']['token_pagseguro']);

        $total = 0;
        $order_item_count = 1;
        if (count($items) > 0) {
            foreach ($items as $item) {
                $productItem = $item['Product'];

                $this->Carrinho->adicionarItem($order_item_count, $productItem['name'], sprintf('%01.2f', $item['Item']['price']), 0, $item['Item']['quantity']);
                $this->Carrinho->setEmail($this->Auth->user('email'));

                $total += $item['Item']['subtotal'];
                $order_item_count++;
            }

//            $this->Carrinho->setTipoPagamento('CREDIT_CARD');
            $shopData['total'] = sprintf('%01.2f', $total);
            $result = $this->Carrinho->finalizaCompra();

            if (strpos($result, 'UNAUTHORIZED') !== false) {
                $this->autoRender = FALSE;
                $this->autoLayout = FALSE;
                $this->layout = 'ajax';

                $this->Session->setFlash('Esta compra não está autorizada. Entre em contato com o vendedor.');
                $this->redirect($this->referer());
            } else {

                $this->Item->Purchase->id = $items[0]['Purchase']['id'];
                $this->Item->Purchase->saveField('total', sprintf('%01.2f', $total));

                $this->Item->Purchase->saveField('payment_url', $result);
                $this->Item->Purchase->saveField('sessionid', '');
                $this->redirect($result);
            }
        } else {
            $shopData['total'] = 0;
        }
    }

    public function _getCart() {
        $this->loadModel('Purchase');
        $purchases = $this->Purchase->find('all', array('conditions' => array('Purchase.sessionid' => $this->Session->id())));
        $shop = array();
        $i = 0;
        foreach ($purchases as $purchase) {
            $shop[$i] = $this->Item->find('all', array('conditions' => array('Item.purchase_id' => $purchase['Purchase']['id'])));

            $quantity = 0;
            $total = 0;
            $order_item_count = 0;

            if (count($shop[$i]) > 0) {
                foreach ($shop[$i] as $item) {
                    $quantity += $item['Item']['quantity'];
                    $total += $item['Item']['subtotal'];
                    $order_item_count++;
                }
                $shopData[$i]['quantity'] = $quantity;
                $shopData[$i]['total'] = sprintf('%01.2f', $total);
            } else {
                $shopData[$i]['quantity'] = 0;
                $shopData[$i]['total'] = 0;
            }
            $i++;
        }
        $carrinho['shop'] = $shop;
        $carrinho['shopData'] = $shopData;
        return $carrinho;
    }

    /**
     * edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function edit() {

        $this->autoRender = false;
        $this->autoLayout = false;
        $this->layout = 'ajax';
        if ($this->request->is('ajax') && ($this->request->is('post') || $this->request->is('put'))) {
            $id = $this->request->data['id'];
            if (!$this->Item->exists($id)) {
                throw new NotFoundException(__('Invalid item'));
            }

            $item = $this->Item->find('first', array('conditions' => array('Item.id' => $id)));
            $item['Item']['quantity'] = $this->request->data['quantity'];
            $item['Item']['subtotal'] = sprintf('%01.2f', $this->request->data['quantity'] * $item['Item']['price']);

            if ($this->Item->save($item, true)) {
                $this->Session->setFlash(__('The item has been saved.'));
                $carrinho = $this->_getCart();
                $shop = $carrinho['shop'];
                $shopData = $carrinho['shopData'];
                $this->set(compact('shop', 'shopData'));
                $this->render('cart', 'ajax');
            } else {
                $this->Session->setFlash(__('The item could not be saved. Please, try again.'));
            }
        }
    }

    public function clear() {
        $shop = $this->Item->Purchase->find('first', array('conditions' => array('Purchase.sessionid' => $this->Session->id())));
        $this->Item->deleteAll(array('Item.purchase_id' => $shop['Purchase']['id']), false);

        $this->Session->renew();
        $this->Session->setFlash('Os itens foram removidos do seu carrinho');
        return $this->redirect('/');
    }

    /**
     * delete method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function delete($id = null) {
        $this->Item->id = $id;
        if (!$this->Item->exists()) {
            throw new NotFoundException(__('Invalid item'));
        }
        $this->request->onlyAllow('post', 'delete');
        if ($this->Item->delete()) {
            $this->Session->setFlash(__('The item has been deleted.'));
        } else {
            $this->Session->setFlash(__('The item could not be deleted. Please, try again.'));
        }
        return $this->redirect(array('action' => 'index'));
    }

    public function validateItem() {
        $id = $this->request->data['Item']['id'];
        $this->Item->id = $id;
        $response = array('success' => FALSE);
        if (!$this->Item->exists()) {
            $response = array('success' => FALSE);
        } else {
            $validated = $this->Item->field('validated', array('Item.id' => $id));
            $this->request->data['Item']['validated'] = ($validated + $this->request->data['Item']['quantityToValidate']);
            if ($this->Item->save($this->request->data)) {
                $response = array('success' => TRUE);
                $this->set(array('response' => $response, '_serialize' => 'response'));
            }
        }
    }

    public function generateTickets($event_id) {
        if ($this->request->is('post') && $event_id) {
            $product = array(
                'Product' => array(
                    'name' => $this->request->data['Item']['name'],
                    'event_id' => $event_id,
                    'quantity' => $this->request->data['Item']['quantity'],
                    'price' => $this->request->data['Item']['price'],
                    'is_active' => '0',
                    'product_type_id' => '1'
            ));
            $this->Item->Product->create();
            $this->Item->Product->save($product);
            $productId = $this->Item->Product->getLastInsertID();

            for ($x = 0; $x < $this->request->data['Item']['quantity']; $x++) {
                $item = array(
                    'Item' => array(
                        'quantity' => '1',
                        'price' => $this->request->data['Item']['price'],
                        'subtotal' => $this->request->data['Item']['price'],
                        'validated' => 0,
                        'product_id' => $productId,
                        'Purchase' => array(
                            'sessionid' => '',
                            'user_id' => $this->Auth->user('id'),
                            'event_id' => $event_id,
                            'total' => $this->request->data['Item']['price'],
                            'is_print_ticket' => '1',
                            'item_quantity' => '3'
                        )
                    )
                );

                $this->Item->create();
                $this->Item->saveAssociated($item, array('deep' => true));
//                $this->Item->Purchase->create();
//                $this->Item->Purchase->save($compra, false);
                $compra = array();
                $compra['Purchase']['id'] = $this->Item->Purchase->getLastInsertId();
                $compra['Purchase']['code'] = AuthComponent::password($compra['Purchase']['id'] . $this->Auth->user('email'));

                $this->Item->Purchase->saveField('code', $compra['Purchase']['code']);
//                $this->Item->create();
//                $this->Item->save($item, array('validate' => true));
            }
            return $this->redirect(array('controller' => 'items', 'action' => 'generatedTickets', 'ext' => 'pdf', $event_id, $productId));
        }

        $this->Item->Product->Behaviors->load('Containable');
        $options = array(
            'conditions' => array('Product.event_id' => $event_id, 'Product.product_type_id' => '1'),
            'contain' => array(
                'Item' => array(
                    'fields' => array('validated')
                )
            ),
            'fields' => array('id', 'product_type_id', 'name', 'quantity', 'price', 'created'),
        );
        $products = $this->Item->Product->find('all', $options);
        $this->set(compact('products'));
    }

    /**
     * generatedTickets method 
     *
     * @return void
     */
    public function generatedTickets($event_id = NULL, $product_id = 0) {

        if ($event_id == NULL) {
            $this->redirect(array('controller' => 'events'));
        } else {
            $conditions = array();
            if ($product_id > 0) {
                $conditions['Product.id'] = $product_id;
            }
            if ($event_id) {
                $conditions['Purchase.event_id'] = $event_id;
                $conditions['Purchase.is_print_ticket'] = '1';
            }
            $this->Item->Behaviors->load('Containable');
            $tickets = $this->Item->find('all', array(
                'conditions' => $conditions,
                'contain' => array(
                    'Product' => array(
                        'fields' => array('name', 'id', 'quantity')
                    ),
                    'Purchase' => array(
                        'EventPurchase' => array(
                            'fields' => array('id', 'name', 'start_date', 'end_date'),
                            'Address' => array(
                                'fields' => array('id', 'street', 'street_number', 'complement', 'neighborhood', 'zip_code', 'latitude', 'longitude'),
                                'City' => array(
                                    'fields' => array('id', 'name'),
                                    'State' => array('fields' => array('id', 'uf'))
                                )
                            )),
                        'fields' => array('id', 'event_id', 'total', 'code')
                    )
                )
            ));
            $this->set('tickets', $tickets);
            if ($this->RequestHandler->ext === 'pdf') {

                // initializing mPDF
                $this->Mpdf->init();

//                // setting filename of output pdf file
                $this->Mpdf->setFilename("ingressos__" . $product_id . ".pdf");
//
//                // setting output to I, D, F, S
                $this->Mpdf->setOutput('D');
//
//                // you can call any mPDF method via component, for example:
//                $this->Mpdf->SetWatermarkText("Draft");
            }
        }
    }

    public function isAuthorized($user) {
        if (!parent::isAuthorized($user)) {
            if (in_array($this->action, array('add', 'addItems', 'cart', 'clear', 'pagseguro', 'edit', 'validateItem'))) {
                return true;
            }
            if (($this->action === 'generateTickets' || $this->action === 'generatedTickets') && isset($this->request->params['pass'][0])) {
                $eventId = $this->request->params['pass'][0];
                return $this->Item->Purchase->EventPurchase->isOwnedBy($eventId, $user['id']);
            }
        }
        return false;
    }

}
