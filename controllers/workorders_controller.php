<?php

class WorkordersController extends AppController {
  public $helpers = array('Ajax', 'Js' => array('Prototype'), 'Paginator', 'Session');
  public $components = array('RequestHandler', 'Session');
  public $uses = array('Workorder', 'Car', 'User', 'Ordertype', 'Message', 'FixedCost', 'VAT');

  function beforeFilter() {
    parent::beforeFilter();
  }

  function index() {
    $this->paginate = array(
      'order' => array(
        'Workorder.datetime_required ASC'
      ),
      'conditions' => array(
        'Workorder.location_id' => $this->Session->read('Auth.User.location_id'),
        'or' => array(
          'or' => array(
            'Workorder.status_id >' => 2,
            'Workorder.status_id <' => 1,

          ),
          'DATE(Workorder.created)' => date('Y-m-d'),
          'DATE(Workorder.datetime_required)' => date('Y-m-d'),
        )
      ),
      'limit' => 50
    );

    $this->setData();

    $statuses = $this->Workorder->Status->find('list', array('order' => array('order')));

    $s = $statuses[0];
    unset($statuses[0]);
    $statuses[5] = $s;

    $this->set('statuses', $statuses);

    $workorders = $this->paginate('Workorder');

    $this->set('workorders', $workorders);
  }

  function add() {
    if (!empty($this->data)) {
      $this->saveWorkorder(0);
    }

    $this->setData();
  }

  function add_service() {
    if (!empty($this->data)) {
      $this->saveWorkorder(0);
    }

    $this->setData('service');
  }

  function saveWorkorder($status = null) {
    $date = $this->data['Workorder']['datetime_required']['date'];
    $this->data['Debug']['date'] =$this->data['Workorder']['datetime_required'];
    $daten = explode('/', $date);

    if (count($daten) == 3) {
      $this->data['Car']['registration'] = str_replace(' ', '', strtoupper($this->data['Car']['registration']));

      $this->data['Workorder']['status_id'] = $status;
      $this->data['Workorder']['location_id'] = $this->Session->read('Auth.User.location_id');
      $this->data['Workorder']['authorised_by_user_id'] = $this->Session->read('Auth.User.id');

      $this->data['Workorder']['datetime_required'] = $daten[2] . '-' . $daten[1] . '-' . $daten[0] . ' ' .
      ($this->data['Workorder']['datetime_required']['am'] == 'am' ? $this->data['Workorder']['datetime_required']['hours'] : ($this->data['Workorder']['datetime_required']['hours'] == 12 ? 12 :$this->data['Workorder']['datetime_required']['hours'] + 12)) . ':' .
      $this->data['Workorder']['datetime_required']['minutes'] . ':00';

      $this->set('data', $this->data);

      if ($this->data['Car']['registration'] == "" and $this->data['Car']['chassis'] == "") {
        $this->Session->setFlash('Please insert car details');
      } else {
        if (!empty($this->data['Ordertype'][0]) || !empty($this->data['Ordertype'][1])) {
          if (date_sensible($this->data['Workorder']['datetime_required']) === true) {
            if ($this->Workorder->saveAll($this->data, array('validate'=>'first', 'atomic' => true))) {
              $this->Session->setFlash('Work Order Saved');
              
              $user = $this->Auth->user();
              
              $data['Message']['message'] = $this->data['Workorder']['notes'];
              $data['Message']['created'] = date('Y-m-d H:i:s');
              $data['Message']['workorder_id'] = $this->Workorder->id;
              $data['Message']['user_id'] = $user['User']['id'];
              
              $this->Message->save($data);
              
              $this->redirect(array('controller' => 'workorders', 'action' => 'index'));
              exit;
            } else {
              $this->Session->setFlash('Unable to save work order');
            }
          } else {
            $this->Session->setFlash('Please check your date and try again.');
          }
        } else {
          $this->Session->setFlash('Please select a work order');
        }
      }
    } else {
      $this->Session->setFlash('Please set a date required');
    }
  }

  function setData($type = 'sales') {
    $conditions = array(
      'conditions' => array(
        'or' => array(
          array('location_id' => $this->Session->read('Auth.User.location_id')),
          array('location_id' => 0)
        ),
        'and' => array(
          'hidden' => 0
        )
      ),
      'order' => array(
        'order'
      )
    );

    $addons = $this->Workorder->Addon->find('list', $conditions);

    if ($type == 'service') {
      $ordertypes = $this->Workorder->Ordertype->find('list', $conditions);
      foreach ($ordertypes as $index => $ordertype) {
        if (substr($ordertype, 0, 2) !== "SC") unset($ordertypes[$index]);
      }

      $departments = $this->Workorder->Department->find('list', $conditions);

      foreach ($departments as $index => $department) {
        if (strpos(strtolower($department), "service") === false) unset($departments[$index]);
      }
    } else {
      $ordertypes = $this->Workorder->Ordertype->find('list', $conditions);
      $departments = $this->Workorder->Department->find('list', $conditions);
    }

    $this->User->recursive = -1;

    $users = $this->User->find('all', array(
      'conditions' => array(
        'User.location_id' => $this->Session->read('Auth.User.location_id'),
        'User.active' => 1
      )
    ));

    $tmpUsrs = array();

    foreach ($users as $index => $user) {
      $tmpUsers[$user['User']['id']] = ucwords($user['User']['firstname'] . ' ' . $user['User']['surname']);
    }

    $users = $tmpUsers;


    $this->set('users', $users);

    $this->User->recursive = -1;

    $vusers = $this->User->find('all', array(
      'conditions' => array(
        'User.location_id' => $this->Session->read('Auth.User.location_id'),
        'User.role_id >=' => 4,
        'User.active' => 1
      )
    ));

    $tmpUsers = array();

    foreach ($vusers as $index => $vuser) {
      $tmpUsers[$vuser['User']['id']] = ucwords($vuser['User']['firstname'] . ' ' . $vuser['User']['surname']);
    }

    $vusers = $tmpUsers;

    $this->set('vusers', $vusers);

    $statuses = $this->Workorder->Status->find('list', array('order' => array('order')));

    $makes = $this->Workorder->Car->Makemodel->findMakes();

    $this->set(compact(
      'ordertypes', 'addons',
      'departments', 'statuses',
      'makes'
    ));
  }

  function update_select() {
    $this->autoRender = false;
    $this->layout = 'ajax';

    $models = $this->Workorder->Car->Makemodel->findModels($this->data['Car']['makes']);
    $this->set(compact('models'));

    $this->render('ajax/update_select');
  }
  
  function update_messages() {
    $this->autoRender = false;
    $this->layout = 'ajax';
    
    if (isset($this->data)) {
      if (isset($this->data['Message'])) {
        if (strlen($this->data['Message']['message']) > 0) {
          $user = $this->Auth->user();
          
          $this->data['Message']['created'] = date('Y-m-d H:i:s');
          $this->data['Message']['workorder_id'] = $this->data['Workorder']['id'];
          $this->data['Message']['user_id'] = $user['User']['id'];
          
          $this->Message->save($this->data);
          
          $this->Workorder->id = $this->data['Workorder']['id'];
          $this->Workorder->saveField('notes', $this->data['Message']['created']);
        }
      }
    }
    
    $messages = $this->Message->getFromWorkorder($this->data['Workorder']['id']);
    $this->set('messages', $messages);
    $this->render('ajax/messages');
  }
  
  function messagesset() {
    $this->layout = '';
    $this->autoRender = false;
    
    $this->Workorder->recursive = -1;
    $workorders = $this->Workorder->find('all');
    
    $data = array();
    foreach ($workorders as $workorder) {
      if (strlen($workorder['Workorder']['notes']) > 5) {
        $data[] = array(
          'created' => date('Y-m-d H:i:s'),
          'workorder_id' => $workorder['Workorder']['id'],
          'user_id' => $workorder['Workorder']['updated_by'] > 0 ? $workorder['Workorder']['updated_by'] : 1,
          'message' => $workorder['Workorder']['notes']
        );
      }
    }
    
    if ($this->Message->saveAll($data)) {
      print('All saved');
    } else {
      print_r($this->Message->invalidFields());
    }
  }

  function update_car_reg() {
    $this->autoRender = false;
    $this->layout = 'ajax';
    $cars = array();
    
    $cars = $this->Workorder->find('all', array(
      'recursive' => 1,
      'conditions' => array(
        'Car.registration' => str_replace(' ', '', $this->data['Car']['registration'])
      )
    ));

    $this->set('cars', $cars);
    $this->set('cars_json', json_encode($cars));

    $this->render('ajax/update_car');
  }

  function update_car_cha() {
    $this->autoRender = false;
    $this->layout = 'ajax';
    
    $cars = array();
    
    $cars = $this->Workorder->find('all', array(
      'recursive' => 1,
      'conditions' => array(
        'Car.chassis' => str_replace(' ', '', $this->data['Car']['chassis'])
      )
    ));
    
    $this->set('cars', $cars);
    $this->set('cars_json', json_encode($cars));

    $this->render('ajax/update_car');
  }

  function search() {
    $this->autoRender = false;

    $this->paginate = array(
      'order' => array(
        'Workorder.datetime_required ASC',
      ),

      'conditions' => array(
        'Workorder.location_id' => $this->Session->read('Auth.User.location_id'),
      ),

      'limit' => 50,
    );

    if (!empty($this->data['Filters']['search'])) {
      $this->paginate['conditions']['or'] = array(
        array('Car.registration LIKE' => $this->data['Filters']['search']),
        array('Car.registration LIKE' => "%".$this->data['Filters']['search']."%"),
        array('Car.registration LIKE' => "%".$this->data['Filters']['search']),
        array('Car.registration LIKE' => $this->data['Filters']['search']."%"),
        array('Car.chassis LIKE' => $this->data['Filters']['search']),
        array('Car.chassis LIKE' => "%".$this->data['Filters']['search']."%"),
        array('Car.chassis LIKE' => "%".$this->data['Filters']['search']),
        array('Car.chassis LIKE' => $this->data['Filters']['search']."%")
      );
    }

    if (!empty($this->data['Filters']['datetime_required_from']) && !empty($this->data['Filters']['datetime_required_to'])) {
      $date = $this->data['Filters']['datetime_required_from']['date'];
      $daten = explode('/', $date);
      if (count($daten) == 3) {
        $this->data['Filters']['datetime_required_from'] = $daten[2] . '-' . $daten[1] . '-' . $daten[0];

        $date = $this->data['Filters']['datetime_required_to']['date'];
        $daten = explode('/', $date);
        $this->data['Filters']['datetime_required_to'] = $daten[2] . '-' . $daten[1] . '-' . $daten[0];

        $this->paginate['conditions']['or'][] = array('Workorder.datetime_required BETWEEN DATE(?) AND DATE(?)' => array($this->data['Filters']['datetime_required_from'], $this->data['Filters']['datetime_required_to']));
      }
    } else {
      $this->paginate['conditions']['or'][] = array(
        'DATE(Workorder.created)' => date('Y-m-d'),
        'DATE(Workorder.datetime_required)' => date('Y-m-d'),
      );
    }

    if (!empty($this->data['Filters']['Department'])) {
      $this->paginate['conditions'][] = array('Department.id' => $this->data['Filters']['Department']);
    }

    if (!empty($this->data['Filters']['Status'])) {
      if ($this->data['Filters']['Status'] == 5) $this->data['Filters']['Status'] = 0;
      $this->paginate['conditions'][] = array('Workorder.status_id' => $this->data['Filters']['Status']);
    } else {
      $this->paginate['conditions']['or'][] = array('Workorder.status_id' => 0);
      $this->paginate['conditions']['or'][] = array('Workorder.status_id > ' => 2);
    }

    if (!empty($this->data['Filters']['User'])) {
      $this->paginate['conditions'][] = array('Workorder.authorised_by_user_id' => $this->data['Filters']['User']);
    }

    $conditions = array(
      'conditions' => array(
        'or' => array(
          array('location_id' => $this->Session->read('Auth.User.location_id')),
          array('location_id' => 0)
        ),
        'and' => array(
          'hidden' => 0
        )
      ),
      'order' => array(
        'order'
      )
    );

    $ordertypes = $this->Workorder->Ordertype->find('list', $conditions);
    $departments = $this->Workorder->Department->find('list', $conditions);
    $statuses = $this->Workorder->Status->find('list', array('order' => array('order')));

    $s = $statuses[0];
    unset($statuses[0]);
    $statuses[5] = $s;

    $this->set('statuses', $statuses);

    $this->set(compact(
      'ordertypes',
      'departments',
      'statuses'
    ));

    $this->User->recursive = -1;

    $users = $this->User->find('all', array(
      'conditions' => array(
        'User.location_id' => $this->Session->read('Auth.User.location_id')
      )
    ));

    $tmpUsrs = array();

    foreach ($users as $index => $user) {
      $tmpUsers[$user['User']['id']] = ucwords($user['User']['firstname'] . ' ' . $user['User']['surname']);
    }

    $users = $tmpUsers;

    $this->set('users', $users);

    $workorders = $this->paginate('Workorder');

    if (!empty($this->data['Filters']['Ordertype'])) {
      foreach($workorders as $i => $workorder) {
        $save = false;
        for ($j = 0; $j < count($workorder['Ordertype']); $j++) {
          if ($workorder['Ordertype'][$j]['id'] == $this->data['Filters']['Ordertype']) $save = true;
        }

        if ($save == false) unset($workorders[$i]);
      }
    }

    $this->set('workorders', $workorders);

    unset($this->data['Filters']['datetime_required_from']);
    unset($this->data['Filters']['datetime_required_to']);

    $this->set('data', $this->data);

    $this->render('index');
  }

  function view($id = null) {
    if (!empty($this->data)) {
      $updated = $this->Session->read('Auth.User.id');

      $new_status = 0;

      if (isset($this->data['action'])) {
        switch($this->data['action']) {
          case 0: // pick up job
            $new_status = 3;
          break;
          case 1: // job completed
            $new_status = 1;
            $this->data['Workorder']['datetime_completed'] = date("Y-m-d H:i:s", strtotime('now'));
            $this->data['Workorder']['completed_by'] = $this->Session->read('Auth.User.id');
            $th = $this->Workorder->read(null, $this->data['Workorder']['id']);
            $updated = $th['Workorder']['updated_by'];
          break;
          case 2: // make urgent
            $new_status = 4;
          break;
          case 3: // cancel work order
            $new_status = 2;
          break;
      	  default:
            $new_status = -1;
      	  break;
        }
      }

      $this->data['Workorder']['updated_by'] = $updated;

      $this->saveWorkorder($new_status > 0 ? $new_status : $this->data['Workorder']['status_id']);
    }

    $conditions = array(
      'conditions' => array(
        'Workorder.id' => $id
      ),

      'limit' => 1,

      'joins' => array(
          array(
          'table' => 'ordertypes_workorders',
          'alias' => 'OrdertypesWorkorder',
          'type' => 'inner',
          'foreignKey' => false,
          'conditions' => array('OrdertypesWorkorder.workorder_id = Workorder.id')
        ),
        array(
          'table' => 'ordertypes',
          'alias' => 'Ordertype',
          'type' => 'inner',
          'foreignKey' => false,
          'conditions' => array('OrdertypesWorkorder.ordertype_id = Ordertype.id')
        )
      )
    );

    $this->setData();

    $workorders = $this->Workorder->find('all', $conditions);
    
    $this->data = $workorders[0];
    $messages = $this->Message->getFromWorkorder($this->data['Workorder']['id']);
    $this->Session->write('Workorder.' . $workorders[0]['Workorder']['id'] . '.read', $workorders[0]['Workorder']['notes']);
    $this->set('messages', $messages);
    $this->set('data', $this->data);
  }
  
  function setup() {
    $ordertypes = $this->Workorder->Ordertype->find('all', array(
      'order' => array('Ordertype.order'),
      'recursive' => -1,
      'conditions' => array(
        'Ordertype.hidden NOT' => 1,
        'Ordertype.location_id' => $this->Session->read('Auth.User.location_id')
      )
    ));
    
    $departments = $this->Workorder->Department->find('all', array(
      'order' => array('Department.order'),
      'recursive' => -1,
      'conditions' => array(
        'Department.hidden NOT' => 1,
        'Department.location_id' => $this->Session->read('Auth.User.location_id')
      )
    ));

    $addons = $this->Workorder->Addon->find('all', array(
      'order' => array('Addon.order'),
      'recursive' => -1,
      'conditions' => array(
        'Addon.hidden NOT' => 1,
        'Addon.location_id' => $this->Session->read('Auth.User.location_id')
      )
    ));
        
    $fixedcosts = $this->FixedCost->find('all', array(
      'order' => array('FixedCost.order'),
      'recursive' => -1,
      'conditions' => array(
        'FixedCost.hidden NOT' => 1,
        'FixedCost.location_id' => $this->Session->read('Auth.User.location_id')
      )
    ));

    $this->set(compact('addons', 'departments', 'ordertypes', 'fixedcosts'));
  }

  function global_setup() {
    $ordertypes = $this->Workorder->Ordertype->find('all', array(
      'order' => array('Ordertype.order'),
      'recursive' => -1,
      'conditions' => array(
        'Ordertype.hidden NOT' => 1,
        'Ordertype.location_id' => 0
      )
    ));

    $departments = $this->Workorder->Department->find('all', array(
      'order' => array('Department.order'),
      'recursive' => -1,
      'conditions' => array(
        'Department.hidden NOT' => 1,
        'Department.location_id' => 0
      )
    ));

    $addons = $this->Workorder->Addon->find('all', array(
      'order' => array('Addon.order'),
      'recursive' => -1,
      'conditions' => array(
        'Addon.hidden NOT' => 1,
        'Addon.location_id' => 0
      )
    ));

    $this->set(compact('addons', 'departments', 'ordertypes'));
    
    $vat = $this->VAT->find('first', array('conditions' => array('VAT.id' => 1))); 
    
    $this->set('vdata', array('vat' => $vat['VAT']['value']));
  }
}

function settax() {
  if ($this->data) {
    $vat = $this->VAT->read(null, 1);
    $vat['VAT']['value'] = $this->data['vat'];
    $this->VAT->save($vat['VAT']);
    header('Location: /workorders/global_setup');
  }
}

function date_sensible($datestr) {
  // this just checks if the date is within six months of the
  // current date, to avoid sticky 1970 date situations and
  // Year 1 BCE situations.

  $time = strtotime($datestr);
  $now = strtotime('now');
  $six_months = strtotime('+6 months', 0);


  return $time + $six_months >= $now && $time < $now + $six_months;
}

?>

