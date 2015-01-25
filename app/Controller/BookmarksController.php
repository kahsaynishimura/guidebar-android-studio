<?php

App::uses('AppController', 'Controller');

/**
 * Bookmarks Controller
 *
 * @property Bookmark $Bookmark
 * @property PaginatorComponent $Paginator
 */
class BookmarksController extends AppController {

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
            $this->Bookmark->create();
            $this->request->data['Bookmark']['user_id'] = $this->Auth->user('id');
            if ($this->Bookmark->save($this->request->data)) {
                return $response = json_encode(array(
                    'success' => true,
                    'id'=>  $this->Bookmark->getLastInsertID(),                        
                    'buttonText' => "<i class='glyphicon glyphicon-star'></i>Remover dos favoritos",
                    'action' => '/bookmarks/delete/' . $this->Bookmark->getLastInsertID()));
            }
        } else {
            return $response = json_encode(array('success' => false,'id'=>0));
        }
    }

    public function myFavorites() {
        $this->Bookmark->Behaviors->load('Containable');
        $options = array(
            'conditions' => array('Bookmark.user_id' => $this->Auth->user('id')),
            'fields' => array('user_id', 'event_id'),
            'order' => 'Event.name',
            'contain' => array(
                'Event' => array(
                    'fields' => array('average_rating', 'user_id', 'is_open_bar', 'views', 'start_date', 'end_date', 'name', 'thumb', 'category_id', 'description', 'minimum_age'),
                    'Category' => array(
                        'fields' => array('name', 'id'),
                    ))
            )
        );
        $bookmarks = $this->Bookmark->find('all', $options);
        if ($this->RequestHandler->ext == 'json') {
            $events = array();
            foreach ($bookmarks as $bookmark) {

                array_push($events, $bookmark);
            }
            $this->set('events', $events);
        } else {
            $this->set('bookmarks', $bookmarks);
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

        $this->Bookmark->id = $id;
        if (!$this->Bookmark->exists()) {
        return $response = json_encode(array('success' => false));
        }
        if ($this->Bookmark->delete()) {
            return $response = json_encode(array(
                'success' => true,
                'buttonText' => "<i class='glyphicon glyphicon-star-empty'></i> Marcar como favorito",
                'action' => '/bookmarks/add/'));
        }
        return $response = json_encode(array('success' => false));
    }

    public function isAuthorized($user) {

        if (!parent::isAuthorized($user)) {

            if ($this->action === 'add' || $this->action === 'myFavorites') {
                return true;
            }
            if (in_array($this->action, array('delete'))) {
                $bookmarkId = $this->request->params['pass'][0];
                return $this->Bookmark->isOwnedBy($bookmarkId, $this->Auth->user('id'));
            }
        }
        return false;
    }

}
