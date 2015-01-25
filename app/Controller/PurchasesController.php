<?php

App::uses('AppController', 'Controller');
App::uses('HttpSocket', 'Network/Http');

/**
 * Purchases Controller
 *
 * @property Purchase $Purchase
 * @property PaginatorComponent $Paginator
 */
class PurchasesController extends AppController {

    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator', 'PagSeguro.Carrinho', 'RequestHandler');

    function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('processNotification', 'finishPayment', 'processNotificationMoip');
    }

    public $transactionStatus = array(
        '1' => array(
            'title' => 'Aguardando pagamento',
            'description' => 'O comprador iniciou a transação, mas até o momento o PagSeguro não recebeu nenhuma informação sobre o pagamento. Obs.: A confirmação do banco pode levar até 4 horas.'
        ),
        '2' => array(
            'title' => 'Em análise',
            'description' => 'O comprador optou por pagar com um cartão de crédito e o PagSeguro está analisando o risco da transação.'
        ),
        '3' => array(
            'title' => 'Paga',
            'description' => 'A transação foi paga pelo comprador e o PagSeguro já recebeu uma confirmação da instituição financeira responsável pelo processamento.'
        ),
        '4' => array(
            'title' => 'Disponível',
            'description' => 'A transação foi paga e chegou ao final de seu prazo de liberação sem ter sido retornada e sem que haja nenhuma disputa aberta.'
        ),
        '5' => array(
            'title' => 'Em disputa',
            'description' => 'O comprador, dentro do prazo de liberação da transação, abriu uma disputa.'
        ),
        '6' => array(
            'title' => 'Devolvida',
            'description' => 'O valor da transação foi devolvido para o comprador.'
        ),
        '7' => array(
            'title' => 'Cancelada',
            'description' => 'A transação foi cancelada sem ter sido finalizada.'
        )
    );

    public function sales($eventId) {
        $this->Purchase->Behaviors->load('Containable');
        $options = array(
            'order' => array('Purchase.created'),
            'conditions' => array('Purchase.event_id' => $eventId, 'Purchase.is_print_ticket' => '0', 'Purchase.total !=' => 0),
            'fields' => array('total', 'item_quantity'),
            'contain' => array(
                'Item' => array(
                    'Product'
                ),
                'UserBuy' => array(
                    'fields' => array('id', 'name', 'filename')
                )
            )
        );
        $this->set('purchases', $this->Purchase->find('all', $options));
        $this->set('transactionStatus', $this->transactionStatus);
    }

    public function myPurchases() {
        $this->loadModel('Event');
        $this->Purchase->Behaviors->load('Containable');
        $options = array(
            'order' => array('Purchase.event_id'),
            'conditions' => array('Purchase.user_id' => $this->Auth->user('id'), 'Purchase.is_print_ticket' => '0'),
            'fields' => array('total', 'event_id', 'id', 'created', 'user_id', 'payment_url', 'item_quantity', 'transaction_code', 'item_quantity', 'code'),
            'contain' => array(
                'EventPurchase' => array(
                    'fields' => array('name', 'id', 'filename')
                ),
                'Item' => array(
                    'fields' => array('quantity', 'price', 'subtotal', 'validated'),
                    'Product' => array(
                        'fields' => array('name', 'id', 'image', 'description')
                    )
                )
            )
        );
        $purchases = $this->Purchase->find('all', $options);
        $this->set('purchases', $purchases);
        $events = array();
        $idsE = array();
        foreach ($purchases as $p) {
            if (!(in_array($p['EventPurchase']['id'], $idsE))) {
                array_push($idsE, $p['EventPurchase']['id']);
                array_push($events, $p['EventPurchase']);
            }
        }
        if ($this->RequestHandler->ext !== 'json') {
            $this->set('events', $events);
            $this->set('transactionStatus', $this->transactionStatus);
        }
    }

    public function processCode($code) {
        if ($code != '') {
//            $nosecret = Security::cipher($code, Configure::read('Security.salt'));

            $this->Purchase->Behaviors->load('Containable');
            $purchase = $this->Purchase->find('first', array('conditions' => array('Purchase.code' => $code),
                'contain' => array(
                    'EventPurchase' => array(
                        'fields' => array('name', 'id', 'filename')
                    ),
                    'UserBuy' => array(
                        'fields' => array('email')
                    ),
                    'Item' => array(
                        'fields' => array('id', 'quantity', 'price', 'subtotal', 'validated'),
                        'Product' => array(
                            'fields' => array('name', 'id', 'image', 'description')
                        )
                    )
            )));
               $this->log($purchase['Purchase']['id'] . $purchase['UserBuy']['email']);
         
               $this->log(AuthComponent::password($purchase['Purchase']['id'] . $purchase['UserBuy']['email']));
               $this->log("Code".$code);
               if (AuthComponent::password($purchase['Purchase']['id'] . $purchase['UserBuy']['email']) == $code) {
                $this->log("2Karina");
                if ($this->request->is('post')) {
                    $purchase['Purchase']['allow_validation'] = true;
                } else {
                    $purchase['Purchase']['allow_validation'] = false;
                    $this->set('transactionStatus', $this->transactionStatus); 
                }
                $this->set('purchase', $purchase);
            }
        }
    }

    public function processNotificationMoip() {
        if ($this->request->is('post')) {
            
        }
    }

    public function processNotification($id = NULL) {
        if ($id != NULL) {
            $this->Purchase->Behaviors->load('Containable');
            $options = array(
                'conditions' => array('Purchase.id' => $id),
                'contain' => array(
                    'EventPurchase' => array(
                        'fields' => array('id'),
                        'User' => array(
                            'fields' => array('email_pagseguro', 'token_pagseguro')
                        )
                    )
                )
            );
            $purchase = $this->Purchase->find('first', $options);
            $this->Purchase->id = $purchase['Purchase']['id'];

            $code = $this->request->data['notificationCode'];
            $email = $purchase['EventPurchase']['User']["email_pagseguro"];
            $token = $purchase['EventPurchase']['User']["token_pagseguro"];
            $url = 'https://ws.pagseguro.uol.com.br/v2/transactions/notifications/' . $code . '?email=' . $email . '&token=' . $token;

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $transaction = curl_exec($curl);
            curl_close($curl);

            if ($transaction != 'Unauthorized') {

                $transaction = simplexml_load_string($transaction);
                $estado_transacao = $transaction->status;
                $codigo_transacao = $transaction->code;

                $this->Purchase->saveField('item_quantity', $estado_transacao);
                $this->Purchase->saveField('transaction_code', $codigo_transacao);
            }
        }
        $this->redirect(array('controller' => 'events', 'action' => 'index'));
    }

    public function finishPayment($id = NULL) {
        if ($id != NULL) {

            $this->Purchase->Behaviors->load('Containable');
            $options = array(
                'conditions' => array('Purchase.id' => $id),
                'contain' => array(
                    'EventPurchase' => array(
                        'User' => array(
                            'fields' => array('email_pagseguro', 'token_pagseguro')
                        )
                    ),
                    'Item' => array(
                        'fields' => array('id', 'quantity')
                    )
                )
            );
            $purchase = $this->Purchase->find('first', $options);
            $totalQuantity = 0;
            foreach ($purchase['Item'] as $value) {
                $this->Purchase->Item->id = $value['id'];
                $totalQuantity+=$value['quantity'];
            }
            $this->Purchase->id = $purchase['Purchase']['id'];
            $this->Purchase->saveField('transaction_code', $this->request->query('transaction_id'));
            $email = $purchase['EventPurchase']['User']["email_pagseguro"];
            $token = $purchase['EventPurchase']['User']["token_pagseguro"];


            $url = 'https://ws.pagseguro.uol.com.br/v2/transactions/' . $this->request->query('transaction_id') . '?email=' . $email . '&token=' . $token;
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $transaction = curl_exec($curl);
            curl_close($curl);

            if ($transaction != 'Unauthorized') {

                $transaction = simplexml_load_string($transaction);
                $estado_transacao = $transaction->status;
                $code = $transaction->code;
                $this->Purchase->saveField('item_quantity', $estado_transacao);
                $this->Purchase->saveField('transaction_code', $code);
            }
        }
        $this->redirect(array('controller' => 'events', 'action' => 'index'));
    }

    public function addMoip() {
        $this->autoRender = FALSE;
        $this->autoLayout = FALSE;
        if ($this->request->is('post') && $this->request->is('ajax')) {
            $items = json_decode($this->request->data['Purchase']['selectedItems'], true);
            $purchase = array(
                'Purchase' => array(
                    'event_id' => $this->request->data['Purchase']['event_id'],
                    'user_id' => $this->Auth->user('id'),
                    'sessionid' => $this->Session->id(),
                    'is_print_ticket' => '0',
                    'item_quantity' => '1',
                )
            );
            $total = 0;
            $purchase['Purchase']['Item'] = array();
            $description = "";
            foreach ($items as $key => $value) {
                $product = $this->Purchase->Item->Product->find('first', array('conditions' => array('Product.id' => $value['Item']['product_id'])));
                $description.=$value['Item']['quantity'] . "x " . $product['Product']['name'] . " ";
                $this->Purchase->Item->Product->id = $product['Product']['id'];

                $this->Purchase->Item->Product->saveField("quantity_available", ( $product['Product']['quantity'] - $value['Item']['quantity']) . "");

                $value['Item']['subtotal'] = sprintf('%01.2f', $product['Product']['price'] * $value['Item']['quantity']);
                $total+=$value['Item']['subtotal'];
                $value['Item']['price'] = sprintf('%01.2f', $product['Product']['price']);
                array_push($purchase['Purchase']['Item'], $value['Item']);
            }
            $purchase['Purchase']['total'] = $total = sprintf('%01.2f', $total);
            $this->Purchase->saveAssociated($purchase, array('deep' => true));
            $this->Purchase->id = $purchase['Purchase']['id'] = $this->Purchase->getLastInsertId();
            $purchase['Purchase']['code'] = AuthComponent::password($purchase['Purchase']['id'] . $this->Auth->user('email'));
            $this->Purchase->saveField('code', $purchase['Purchase']['code']);

            $this->Purchase->Item->Product->Event->Behaviors->load("Containable");
            $event = $this->Purchase->Item->Product->Event->find('first', array(
                'conditions' => array('Event.id' => $purchase['Purchase']['event_id']),
                'contain' => array(
                    'User' => array(
                        'fields' => array('email_pagseguro', 'token_pagseguro')
                    )
                )
            ));
            $response = array();

            if ($total <= 0) {
                $response = array(
                    'success' => true,
                    'valor' => "0",
                );
                $this->Purchase->saveField('item_quantity', 3);
            } else {
                $response = array(
                    'success' => true,
                    'id_carteira' => $event['User']['email_pagseguro'],
                    'email_comprador' => $this->Auth->user('email'),
                    'id_transacao' => $purchase['Purchase']['id'],
                    'nome' => 'Compras no guideBAR',
                    'valor' => str_replace(".", "", $total),
                    'descricao' => substr($description, 0, 255)
                );
            }

            return json_encode($response);
        }
        return json_encode(array('success' => false));
    }

    public function add() {
        if ($this->request->is('post')) {
            $items = json_decode($this->request->data['Purchase']['selectedItems'], true);
            $purchase = array(
                'Purchase' => array(
                    'event_id' => $this->request->data['Purchase']['event_id'],
                    'user_id' => $this->Auth->user('id'),
                    'sessionid' => $this->Session->id(),
                    'is_print_ticket' => '0',
                    'item_quantity' => '1',
                )
            );
            $total = 0;
            $purchase['Purchase']['Item'] = array();
            foreach ($items as $key => $value) {
                $product = $this->Purchase->Item->Product->find('first', array('conditions' => array('Product.id' => $value['Item']['product_id'])));

                $this->Purchase->Item->Product->id = $product['Product']['id'];

                $this->Purchase->Item->Product->saveField("quantity_available", ( $product['Product']['quantity'] - $value['Item']['quantity']) . "");

                $value['Item']['subtotal'] = sprintf('%01.2f', $product['Product']['price'] * $value['Item']['quantity']);
                $total+=$value['Item']['subtotal'];
                $value['Item']['price'] = sprintf('%01.2f', $product['Product']['price']);
                array_push($purchase['Purchase']['Item'], $value['Item']);
                $this->Carrinho->adicionarItem($key + 1, $product['Product']['name'], sprintf('%01.2f', $value['Item']['price']), 0, $value['Item']['quantity']);
            }
            $purchase['Purchase']['total'] = $total;
            $this->Purchase->saveAssociated($purchase, array('deep' => true));
            $this->Purchase->id = $purchase['Purchase']['id'] = $this->Purchase->getLastInsertId();
            $purchase['Purchase']['code'] = AuthComponent::password($purchase['Purchase']['id'] . $this->Auth->user('email'));
            $this->Purchase->saveField('code', $purchase['Purchase']['code']);

            $this->Purchase->Item->Product->Event->Behaviors->load("Containable");
            $event = $this->Purchase->Item->Product->Event->find('first', array(
                'conditions' => array('Event.id' => $purchase['Purchase']['event_id']),
                'contain' => array(
                    'User' => array(
                        'fields' => array('email_pagseguro', 'token_pagseguro')
                    )
                )
            ));
            $this->Carrinho->setUrlRetorno('http://guidebar.com.br/purchases/finishPayment/' . $purchase['Purchase']['id']);
            $this->Carrinho->setUrlNotificacao('http://guidebar.com.br/purchases/processNotification/' . $purchase['Purchase']['id']);
            $this->Carrinho->setCredenciais($event['User']['email_pagseguro'], $event['User']['token_pagseguro']);
            $this->Carrinho->setEmail($this->Auth->user('email'));
            if ($total > 0) {
                $result = $this->Carrinho->finalizaCompra();
                if (strpos($result, 'UNAUTHORIZED') !== false || $result=="" ) {
                    $this->Session->setFlash(_('Não foi possível completar a compra. Entre em contato com o produtor do evento pelo formulário "Fale com o Promotor"'));
                    $this->redirect($this->referer());
                } else { 
                    $this->Purchase->saveField('payment_url', $result);
                    $this->Purchase->saveField('sessionid', '');
                    $this->redirect($result);
                }
            } else {
                $this->Purchase->saveField('item_quantity', 3);
            }
        }
    }

    public function isAuthorized($user) {
        if (!parent::isAuthorized($user)) {
            if (in_array($this->action, array('myPurchases', 'processCode', 'add', 'addMoip'))) {
                return true;
            }
            if (in_array($this->action, array('sales'))) {
                $eventId = $this->request->params['pass'][0];
                return $this->Purchase->EventPurchase->isOwnedBy($eventId, $this->Auth->user('id'));
            }
        }
        return false;
    }

}
