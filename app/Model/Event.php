<?php

App::uses('AppModel', 'Model');

/**
 * Event Model
 *
 * @property Category $Category
 * @property User $User
 * @property Purchase $Purchase
 * @property Address $Address
 * @property Attendance $Attendance
 * @property Bookmark $Bookmark
 * @property Comment $Comment
 * @property Complaint $Complaint
 * @property Evaluation $Evaluation
 * @property Product $Product
 * @property Sponsor $Sponsor
 */
class Event extends AppModel {

    public function compareDates() {
        return strtotime($this->data[$this->alias]['start_date']) <
                strtotime($this->data[$this->alias]['end_date']);
    }

    public function beforeToday() {
        return strtotime($this->data[$this->alias]['start_date']) >
                (strtotime(date('Y-m-d H:00:00')));
    }

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
        'category_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
        ),
        'minimum_age' => array(
            'numeric' => array(
                'rule' => array('numeric'),
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
            'range' => array(
                'rule' => array('range', -1, 130),
                'message' => 'Tente algo entre 0 e 130 anos.'
            )
        ),
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
            'message' => 'Este campo precisa ser preenchido',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
        ), 'description' => array(
            'notempty' => array(
                'rule' => array('notempty'),
            'message' => 'Este campo precisa ser preenchido',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
        ),
        'start_date' =>
        array(
            'datetime' => array(
                'rule' => array('datetime'),
            'message' => 'Escolha uma data válida',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
            'compareDates' => array(
                'rule' => 'compareDates',
                'message' => 'A data inicial deve ser menor que a data final',
            ),
            'beforeToday' => array(
                'rule' => 'beforeToday',
                'message' => 'Informe uma data no futuro'
            ),
        ),
        'end_date' => array(
            'datetime' => array(
                'rule' => array('datetime'),
                'message' => 'Escolha uma data válida',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
        )
    );

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = array(
        'Category' => array(
            'className' => 'Category',
            'foreignKey' => 'category_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Address' => array(
            'className' => 'Address',
            'foreignKey' => 'address_id'
        ),
    );

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = array(
        'Attendance' => array(
            'className' => 'Attendance',
            'foreignKey' => 'event_id',
            'dependent' => false,
        ),
        'Purchase' => array(
            'className' => 'Purchase',
            'foreignKey' => 'event_id',
            'dependent' => false,
        ),
        'Bookmark' => array(
            'className' => 'Bookmark',
            'foreignKey' => 'event_id',
        ),
        'Complaint' => array(
            'className' => 'Complaint',
            'foreignKey' => 'event_id',
        ),
        'Evaluation' => array(
            'className' => 'Evaluation',
            'foreignKey' => 'event_id',
            'dependent' => false,
        ),
        'Product' => array(
            'className' => 'Product',
            'foreignKey' => 'event_id',
            'dependent' => false,
        ),
        'Sponsor' => array(
            'className' => 'Sponsor',
            'foreignKey' => 'event_id',
            'dependent' => false,
        )
    );

    public function isOwnedBy($event, $user) {
        return $this->field('id', array('id' => $event, 'user_id' => $user)) === $event;
    }

    public function updateViews($events) {

        if (!isset($events[0])) {
            $a = $events;
            unset($events);
            $events[0] = $a;
        }

        $this->unbindModel(
                array('belongsTo' => array('Category'))
        );

        $eventIds = Set::extract('/Event/id', $events);

        $this->updateAll(
                array(
            'Event.views' => 'Event.views + 1',
                ), array('Event.id' => $eventIds)
        );
    }

}
