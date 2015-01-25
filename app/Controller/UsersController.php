<?php

App::uses('AppController', 'Controller');
App::uses('CakeEmail', 'Network/Email');

/**
 * Users Controller
 *
 * @property User $User
 * @property PaginatorComponent $Paginator
 */
class UsersController extends AppController {

    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator', 'Uploads', 'RequestHandler');

    /**
     * users method
     *
     * @return void
     */
    public function users() {
        $this->User->recursive = 0;
        $this->set('users', $this->Paginator->paginate());
    }

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('empregos', 'add', 'confirmation', 'logout', 'login', 'loginfb', 'recover_password', 'change_password');
    }

    public function details($id = null) {
        if (!$this->User->exists($id)) {
            throw new NotFoundException(__('Invalid user'));
        }
        $options = array('conditions' => array('User.id' => $id));
        $user = $this->User->find('first', $options);
        $this->set('user', $user);
    }

    /**
     * add method
     *
     * @return void
     */
    public function add() {
        $this->Auth->logout();
        if ($this->request->is('post')) {
            if ($this->RequestHandler->ext == 'json') {
                $this->request->data['User']['filename'] = 'icone_transparente.png';
            } else {
                $image = $this->request->data['User']['filename'];
                if (empty($image['name'])) {
                    $this->request->data['User']['filename'] = 'icone_transparente.png';
                } else {
                    $this->request->data['User']['filename'] = $image['name'];
                }
            }
            $key = md5(date('mdY') . rand(4000000, 4999999));
            $this->request->data['User']['confirmation_key'] = $key;

            $secret = Security::cipher($this->request->data['User']['email'] . date('d-m-Y H:i:s'), Configure::read('Security.salt'));
            $result = base64_encode($secret);

            $this->request->data['User']['access_token'] = $result;


            $this->User->create();
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('Novo usuário criado com sucesso.'));
                $id = $this->User->getLastInsertID();
                if ($this->RequestHandler->ext != 'json' && !empty($image['name'])) {
                    $this->User->id = $id;
                    $upload = $this->Uploads->upload($image, 'img/Usuarios/' . $id, "USER_" . $id . "." . pathinfo($image['name'], PATHINFO_EXTENSION));
                    $this->request->data['User']['filename'] = str_replace("img/", "", $upload);
                    $this->User->saveField('filename', $this->request->data['User']['filename'], true);
                    $this->request->data['User']['thumb'] = str_replace("img/", "", "Usuarios/" . $id . "/thumb/" . "USER_" . $id . "." . pathinfo($image['name'], PATHINFO_EXTENSION));

                    $this->User->saveField('thumb', $this->request->data['User']['thumb'], true);
                }


                //Email

                $Email = new CakeEmail('smtp');
                $Email->from(array('thiago@guidebar.com.br' => 'Guidebar'))
                        ->to($this->request->data['User']['email'] . '')
                        ->subject('Guidebar - Confirmação de cadastro')
                        ->template('confirmation', 'default')
                        ->emailFormat('html')
                        ->viewVars(array('email' => $this->request->data['User']['email'], 'key' => $key, 'userName' => $this->request->data['User']['name']))
                        ->send();
                $this->Session->setFlash('Acesse seu e-mail para confirmar o cadastro');
                if ($this->RequestHandler->ext == 'json') {
                    $this->set('response', array('success' => true, 'id' => $id, 'errors' => ''));
                } else {
                    $this->redirect(array('action' => 'login'));
                }
            } else {
                $this->set('response', array('success' => false, 'id' => 0, 'errors' => $this->User->validationErrors));
                $this->Session->setFlash(__('Não foi possível criar sua conta. Por favor, tente novamente.'));
            }
        }
    }

    public function confirmation() {
        $this->autoLayout = false;
        $this->autoRender = false;
        if ($this->request->query('key') && $this->request->query('email')) {
            $user = $this->User->find('first', array('conditions' => array('User.email' => $this->request->query('email'))));
            if ($user['User']['confirmation_key'] === $this->request->query('key')) {
                $this->User->id = $user['User']['id'];
                $this->User->saveField('is_active', '1');

                $this->Session->setFlash('Seu cadastro foi ativado com sucesso.');
                $this->Auth->login($user['User']);
                $this->redirect($this->Auth->redirect());
            } else {
                $this->Session->setFlash('Não foi possível confirmar seu cadastro.');
                $this->redirect(array('controller' => 'users', 'action' => 'login'));
            }
        } else {
            $this->redirect(array('controller' => 'users', 'action' => 'login'));
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
        if (!$this->User->exists($id)) {
            throw new NotFoundException(__('Invalid user'));
        }

        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->RequestHandler->ext !== 'json') {
                $image = $this->request->data['User']['filename'];
            }
            $fieldList = array('name', 'date_of_birth', 'gender', 'token_pagseguro', 'email_pagseguro', 'email_moip', 'is_selling_ticket_pagseguro', 'is_selling_ticket_moip');
            if (!empty($image['name'])) {
                $upload = $this->Uploads->upload($this->request->data['User']['filename'], 'img/Usuarios/' . $id, "USER_" . $id . "." . pathinfo($image['name'], PATHINFO_EXTENSION));
                $this->request->data['User']['filename'] = str_replace("img/", "", $upload);
                $this->request->data['User']['thumb'] = str_replace("img/", "", "Usuarios/" . $id . "/thumb/" . "USER_" . $id . "." . pathinfo($image['name'], PATHINFO_EXTENSION));

                array_push($fieldList, 'filename');
                array_push($fieldList, 'thumb');
            }
            if ($this->User->save($this->request->data, true, $fieldList)) {
                $this->Session->setFlash(__('Seus dados foram salvos.'));
                $this->Auth->login($this->request->data['User']);
                if ($this->RequestHandler->ext == 'json') {
                    $this->set('response', array('success' => true, 'id' => $id, 'errors' => ''));
                } else {
                    return $this->redirect(array('action' => 'details', $id));
                }
            } else {
                $this->set('response', array('success' => false, 'id' => 0, 'errors' => $this->User->validationErrors));
                $this->Session->setFlash(__('Não foi possível salvar seus.'));
            }
        } else {
            $options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
            $this->request->data = $this->User->find('first', $options);
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
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        $this->request->onlyAllow('post', 'delete');
        if ($this->User->delete()) {
            $this->Session->setFlash(__('Sua conta foi removida'));
        } else {
            $this->Session->setFlash(__('Não foi possível remover sua conta. Por favor, tente novamente.'));
        }
        return $this->redirect(array('action' => 'index'));
    }

    public function recover_password() {
        if ($this->request->is('post')) {
            if ($this->RequestHandler->ext !== 'json') {
                $this->layout = 'login_wrapper';
            }
            $user = $this->User->find('first', array('conditions' => array('User.email' => $this->request->data['User']['email'])));
            
            if (empty($user['User'])) {
                $this->set('response', array('success' => false, 'data' => 'Este e-mail não está cadastrado no guidebar.'));
                $this->User->validationErrors['email'] = "Este e-mail não está cadastrado no guidebar.";
            } else {
                $this->User->id = $user['User']['id'];
                $key = md5(date('mdY') . rand(4000000, 4999999));
                if ($this->User->saveField('recover_key', $key)) {
                    $Email = new CakeEmail('smtp');
                    $Email->from(array('thiago@guidebar.com.br' => 'Guidebar'))
                            ->to($this->request->data['User']['email'] . '')
                            ->subject('Guidebar - Recuperação de senha')->template('recover_password', 'default')
                            ->emailFormat('html')
                            ->viewVars(array('key' => $key, 'email' => $user['User']['email']))
                            ->send();
                    $this->Session->setFlash('Acesse o link enviado para ' . $user['User']['email']);
                    $this->set('response', array('success' => true, 'data' => 'Acesse o link enviado para ' . $user['User']['email']));
                } else {
                    $this->Session->setFlash('Não foi possível enviar o e-mail de recuperação de senha.');
                    $this->set('response', array('success' => false, 'data' => 'Não foi possível enviar o e-mail de recuperação de senha.'));
                }
            }
        }
    }

    public function change_password() {
        if ($this->RequestHandler->ext !== 'json') {
            $this->layout = 'login_wrapper';
        }
        if ($this->request->query('key') && $this->request->query('email')) {
            $user = $this->User->find('first', array('conditions' => array('User.email' => $this->request->query('email'))));
            if ($user['User']['recover_key'] === $this->request->query('key')) {
                $this->Session->write('recoverUser', $user);
            } else {
                $this->Session->setFlash('Esta chave expirou. Tente novamente');
                $this->redirect(array('controller' => 'users', 'action' => 'recover_password'));
            }
        } elseif ($this->request->is('post')) {

            $user = $this->Session->read('recoverUser');

            if ($user) {
                $data = $user;
                $data['User']['password'] = $this->request->data['User']['password'];
                $data['User']['recover_key'] = '0';
                $data['User']['is_active'] = '1';
                $this->User->id = $user['User']['id'];
                if ($this->request->data['User']['password'] == $this->request->data['User']['password_confirm']) {

                    if ($this->User->save($data, array('validate' => true))) {
                        $this->Session->setFlash('Sua senha foi alterada com sucesso.');
                        $this->redirect(array('controller' => 'users', 'action' => 'login'));
                    }
                } else {
                    $this->User->validationErrors['password_confirm'] = 'Deve ser igual à senha';
                }
            }
        } else {
            $this->redirect(array('controller' => 'users', 'action' => 'login'));
        }
    }

    public function login() {
        if ($this->request->is('post')) {
            if ($this->Auth->login()) {
                if ($this->Auth->user('is_active') == 0) {
                    $this->Session->setFlash(__('É preciso confirmar seu cadastro para acessar o sistema'), 'default', array(), 'auth');
                    $this->Auth->logout();
                } else if ($this->Auth->user('is_active') == 1) {
                    if ($this->RequestHandler->ext == 'json') {
                        $user = array();
                        $user['id'] = $this->Auth->user('id');
                        $user['email'] = $this->Auth->user('email');
                        $user['is_active'] = $this->Auth->user('is_active');
                        $user['name'] = $this->Auth->user('name');
                        $user['id_facebook'] = $this->Auth->user('id_facebook');
                        $user['email_pagseguro'] = $this->Auth->user('email_pagseguro');
                        $user['token_pagseguro'] = $this->Auth->user('token_pagseguro');
                        $user['access_token'] = $this->Auth->user('access_token');
                        $this->set(array('user' => $user, '_serialize' => 'user'));
                    } else {
                        $this->layout = 'login_wrapper';
                        $this->set('user_login', $this->Auth->user);
                        $this->redirect(array('controller' => 'events', 'action' => 'index'));
                    }
                }
            } else {
                $this->Session->setFlash(__('E-mail ou senha inválidos. Tente novamente.'), 'default', array(), 'auth');
            }
        } else {
            $this->layout = 'login_wrapper';
        }
    }

    public function loginfb() {
        $this->autoRender = FALSE;
        $this->autoLayout = FALSE;
        if ($this->request->is('ajax') || ($this->request->is('post') && $this->RequestHandler->ext === 'json')) {

            $this->layout = 'ajax';
            $user = $this->User->find('first', array('recursive' => -1,
                'conditions' => array('User.email' => $this->data['email'])));
            if ($user) {
                $this->User->id = $user['User']['id'];
                $this->User->saveField('id_facebook', $this->data['id']);

                $user['User']['id_facebook'] = $this->data['id'];
                if ($this->RequestHandler->ext === 'json') {
                    $this->layout = 'json';
                    $this->autoRender = TRUE;
                    $this->autoLayout = TRUE;

                    $this->set(array('user' => $user['User'], '_serialize' => 'user'));
                } else {
                    $this->Auth->login($user['User']);
                }
            } else {
                $birthday = $this->data['birthday'];
                $birthday = date("Y-m-d", strtotime($birthday));
                $gender = $this->data['gender'] == 'female' || $this->data['gender'] == '1' ? 1 : 2;
                $data['User'] = array(
                    'email' => $this->data['email'], # Normally Unique
                    'password' => AuthComponent::password(uniqid(md5(mt_rand()))), # Set random password
                    'name' => $this->data['name'],
                    'date_of_birth' => $birthday,
                    'gender' => $gender,
                    'id_facebook' => $this->data['id'],
                    'is_active' => 1,
                    'email_pagseguro' => '',
                    'token_pagseguro' => '',
                    'filename' => $this->data['filename'],
                    'thumb' => $this->data['filename']
                );
                $this->User->create();
                $this->User->clear();
                if ($this->User->save($data, array('validate' => true))) {
                    $data['User']['id'] = $this->User->getLastInsertID();
                    $secret = Security::cipher($data['User']['email'] . date('d-m-Y H:i:s'), Configure::read('Security.salt'));
                    $result = base64_encode($secret);

                    $data['User']['access_token'] = $result;
                    $this->User->saveField('access_token', $result);
                    $this->Auth->login($data['User']);
                    if ($this->request->is('post') && $this->RequestHandler->ext === 'json') {
                        $this->layout = 'json';
                        $this->autoRender = TRUE;
                        $this->autoLayout = TRUE;
                        $this->set(array('user' => $data['User'], '_serialize' => 'user'));
                    }
                }
            }
        }
    }

    public function logout() {
        $this->redirect($this->Auth->logout());
    }

    public function empregos() {
        if ($this->request->is('post')) {
            $Email = new CakeEmail('smtp');
            $Email->from(array('thiago@guidebar.com.br' => 'Empregos'))
                    ->to('thiago@guidebar.com.br')
                    ->subject('Guidebar - Empregos')
                    ->send('Enviado por ' . $this->request->data['User']['name'] . ' - ' .
                            $this->request->data['User']['email'] . '<br /><br />' . $this->request->data['User']['description']);

            $this->Session->setFlash("Um e-mail foi enviado com suas informações. Obrigado por nos contactar. ");
        }

        $this->layout = 'login_wrapper';
    }

    function setDadosPagamento() {

        $this->autoRender = FALSE;
        $this->autoLayout = FALSE;

        $this->User->id = $this->Auth->user('id');
        $user = array(
            'User' => array(
                'email_pagseguro' => $this->request->data['User']['email_pagseguro'],
                'token_pagseguro' => $this->request->data['User']['token_pagseguro'],
                'email_moip' => $this->request->data['User']['email_moip'],
                'is_selling_ticket_pagseguro' => $this->request->data['User']['is_selling_ticket_pagseguro'],
                'is_selling_ticket_moip' => $this->request->data['User']['is_selling_ticket_moip'],
                
            )
        );
        if ($this->User->save($user, array('validate' => false))) {
            return json_encode(array('success' => true));
        } else {
            return json_encode(array('success' => false));
        }
    }

    public function isAuthorized($user) {
        if (!parent::isAuthorized($user)) {
            if ($this->action === 'add') {
                return true;
            }
            if (in_array($this->action, array('users', 'details', 'setDadosPagamento'))) {
                return true;
            }
            if (in_array($this->action, array('edit', 'delete'))) {
                $userId = $this->request->params['pass'][0];
                return $this->Auth->user('id') === $userId;
            }
        }
        return false;
    }

}
