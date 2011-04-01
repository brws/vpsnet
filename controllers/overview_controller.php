<?php
/**
*
*/
class OverviewController extends AppController {
  public $helpers = array('Ajax', 'Js' => array('Prototype'), 'Paginator');
  public $components = array('RequestHandler', 'Session');
  var $uses = array('Workorder', 'User', 'Role', 'FixedCost');

  function index() {
    if (!$this->Role->atleast($this->Role->DEALER_ADMIN)) {
      $this->Session->setFlash('You do not have access to the reports page');
      $this->redirect('/');
      exit;
    }

    $this->paginate = array(
      'fields' => array(
        'COUNT(DISTINCT Workorder.id) as count',
        'MONTH(Workorder.created) as month', 'YEAR(Workorder.created) as year',
        'MONTHNAME(Workorder.created) as monthname',
        'DAY(LAST_DAY(Workorder.created)) as lastday',
        'SUM(Ordertype.cost) as ocost',
        'SUM(Ordertype.charge) as ocharge'
      ),

      'order' => array(
        'Workorder.created DESC'
      ),

      'conditions' => array(
        'Workorder.location_id' => $this->Session->read('Auth.User.location_id'),
        'Workorder.status_id' => 1
      ),

      'limit' => 50,

      'joins' => array(
        array(
          'table' => 'ordertypes_workorders',
          'alias' => 'OrdertypesWorkorder',
          'type' => 'left',
          'foreignKey' => false,
          'conditions' => array('OrdertypesWorkorder.workorder_id = Workorder.id')
        ),

        array(
          'table' => 'ordertypes',
          'alias' => 'Ordertype',
          'type' => 'left',
          'foreignKey' => false,
          'conditions' => array('OrdertypesWorkorder.ordertype_id = Ordertype.id')
        )
      ),

      'group' => array('MONTH(Workorder.created)', 'YEAR(Workorder.created)')
    );

    $workorders = $this->paginate('Workorder');

    $this->paginate = array(
      'fields' => array(
        'COUNT(DISTINCT Workorder.id) as count',
        'MONTH(Workorder.created) as month', 'YEAR(Workorder.created) as year',
        'MONTHNAME(Workorder.created) as monthname',
        'DAY(LAST_DAY(Workorder.created)) as lastday',
        'SUM(Addon.cost) as acost',
        'SUM(Addon.charge) as acharge'
      ),

      'order' => array(
        'Workorder.created DESC'
      ),

      'conditions' => array(
        'Workorder.location_id' => $this->Session->read('Auth.User.location_id'),
        'Workorder.status_id' => 1
      ),

      'limit' => 50,

      'joins' => array(
        array(
          'table' => 'addons_workorders',
          'alias' => 'AddonsWorkorder',
          'type' => 'left',
          'foreignKey' => false,
          'conditions' => array('AddonsWorkorder.workorder_id = Workorder.id')
        ),

        array(
          'table' => 'addons',
          'alias' => 'Addon',
          'type' => 'left',
          'foreignKey' => false,
          'conditions' => array('AddonsWorkorder.addon_id = Addon.id')
        )
      ),

      'group' => array('MONTH(Workorder.created)', 'YEAR(Workorder.created)')
    );

    App::import('Set');

    $workorders = Set::merge($workorders, $this->paginate('Workorder'));

    // show monthly statements, work order counts and costings, with a "complete list" button

    $users = $this->User->find('list', array('conditions' => array('User.location_id' => $this->Session->read('Auth.User.location_id'))));
    $this->set('users', $users);

    $this->set('workorders', $workorders);

    $this->setData();
  }

  function setData() {
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
    $addons = $this->Workorder->Addon->find('list', $conditions);
    $departments = $this->Workorder->Department->find('list', $conditions);
    $statuses = $this->Workorder->Status->find('list', array('order' => array('order')));

    $makes = $this->Workorder->Car->Makemodel->findMakes();

    $this->set(compact(
      'ordertypes', 'addons',
      'departments', 'statuses',
      'makes'
    ));
  }

  function complete($m = null, $y = null) {
    $this->paginate = array(
      'fields' => array(
        'COUNT(DISTINCT Workorder.id) AS `count`',
        'MONTH(Workorder.created) as `month`',
        'MONTHNAME(Workorder.created) as `monthname`',
        'DAY(Workorder.created) as `day`',
        'YEAR(Workorder.created) as `year`',
        'SUM(Ordertype.cost) as `ocost`',
        'SUM(Ordertype.charge) as `ocharge`',
      ),

      'order' => array(
        'Workorder.created DESC'
      ),

      'conditions' => array(
        'Workorder.location_id' => $this->Session->read('Auth.User.location_id'),
        'MONTH(Workorder.created)' => $m,
        'YEAR(Workorder.created)' => $y,
        'Workorder.status_id' => 1
      ),

      'limit' => 50,

      'joins' => array(
        array(
          'table' => 'ordertypes_workorders',
          'alias' => 'OrdertypesWorkorder',
          'type' => 'LEFT',
          'foreignKey' => false,
          'conditions' => array('OrdertypesWorkorder.workorder_id = Workorder.id')
        ),

        array(
          'table' => 'ordertypes',
          'alias' => 'Ordertype',
          'type' => 'LEFT',
          'foreignKey' => false,
          'conditions' => array('OrdertypesWorkorder.ordertype_id = Ordertype.id')
        ),
      ),

      'group' => array('DAY(Workorder.created)', 'MONTH(Workorder.created)', 'YEAR(Workorder.created)')
    );

    // show monthly statements, work order counts and costings, with a "complete list" button

    $workorders = $this->paginate('Workorder');

    $this->paginate = array(
      'fields' => array(
        'COUNT(DISTINCT Workorder.id) AS `count`',
        'MONTH(Workorder.created) as `month`',
        'MONTHNAME(Workorder.created) as `monthname`',
        'DAY(Workorder.created) as `day`',
        'YEAR(Workorder.created) as `year`',
        'SUM(Addon.cost) as `acost`',
        'SUM(Addon.charge) as `acharge`',
      ),

      'order' => array(
        'Workorder.created DESC'
      ),

      'conditions' => array(
        'Workorder.location_id' => $this->Session->read('Auth.User.location_id'),
        'MONTH(Workorder.created)' => $m,
        'YEAR(Workorder.created)' => $y,
        'Workorder.status_id' => 1
      ),

      'limit' => 50,

      'joins' => array(

        array(
          'table' => 'addons_workorders',
          'alias' => 'AddonsWorkorder',
          'type' => 'left',
          'foreignKey' => false,
          'conditions' => array('AddonsWorkorder.workorder_id = Workorder.id')
        ),

        array(
          'table' => 'addons',
          'alias' => 'Addon',
          'type' => 'left',
          'foreignKey' => false,
          'conditions' => array('AddonsWorkorder.addon_id = Addon.id')
        )

      ),

      'group' => array('DAY(Workorder.created)', 'MONTH(Workorder.created)', 'YEAR(Workorder.created)')
    );

    App::import('Set');

    $workorders = Set::merge($workorders, $this->paginate('Workorder'));

    $this->set('workorders', $workorders);

    $this->setData();
  }
  
  function print_report($m = null, $y = null) {
    $this->layout = 'report';
    $this->all($y . '-' . $m . '-1', true);
  }
  
  /*
  <option value="last_month">Last Month</option>
  <option value="last_week">Last Week</option>
  <option value="this_month">This Month</option>
  <option value="this_week">This Week</option>
  <option value="today">Today</option>
  <option value="yesterday">Yesterday</option>
  */
  
  function valeter() {
  
    $s = isset($_GET['s']) ? $_GET['s'] : 0;
    $e = isset($_GET['e']) ? $_GET['e'] : 0;
    
    $dates = array();
    $dates['this_week'] = 'WEEK(datetime_completed) = WEEK(NOW()) AND YEAR(datetime_completed) = YEAR(NOW())';
    $dates['this_month'] = 'MONTH(datetime_completed) = MONTH(NOW()) AND YEAR(datetime_completed) = YEAR(NOW())';
    $dates['last_week'] = 'CURDATE() - INTERVAL DAYOFWEEK(CURDATE()) DAY + INTERVAL (DAYOFWEEK(CURDATE())<=1)*-7 + 1 DAY';
    $dates['last_month'] = 'datetime_completed => '.mktime(0,0,0,date('m')-1,1,date('Y')).' AND datetime_completed < '.mktime(0,0,0,date('m'),1,date('Y'));
    $dates['today'] = 'YEAR(datetime_completed) = YEAR(NOW()) AND MONTH(datetime_completed) = MONTH(NOW()) AND DAY(datetime_completed) = DAY(NOW())';
    $dates['yesterday'] = 'YEAR(datetime_completed) = YEAR(NOW()) AND MONTH(datetime_completed) = MONTH(NOW()) AND DAY(datetime_completed) = DAY(NOW())-1';
    
    $dates['custom'] = "datetime_completed >= {$s} AND datetime_completed <= {$e}";
    
    $modifier = isset($_GET['w']) ? $_GET['w'] : 'this_week';
    
    //$query = "select count(*) as `valets`, users.*, ordertypes.* FROM workorders INNER JOIN ordertypes_workorders ON (ordertypes_workorders.workorder_id = workorders.id) INNER JOIN ordertypes ON (ordertypes.id = ordertypes_workorders.ordertype_id) INNER JOIN users ON (users.id = workorders.authorised_by_user_id) WHERE workorders.status_id = 1 and workorders.location_id = 2 AND {$dates[$modifier]} GROUP BY ordertypes.id, workorders.completed_by";
    
    if (isset($_GET['u'])) {
      $user = 'AND workorders.completed_by = ' . $_GET['u'];
    } else $user = '';
    
    $query = "select count(*) as `valets`, ordertypes.* FROM workorders INNER JOIN ordertypes_workorders ON (ordertypes_workorders.workorder_id = workorders.id) INNER JOIN ordertypes ON (ordertypes.id = ordertypes_workorders.ordertype_id) WHERE workorders.status_id = 1 and workorders.location_id = 2 AND {$dates[$modifier]} {$user} GROUP BY ordertypes.id";
    
    debug($query);

    $data = $this->Workorder->query($query);
    
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
    
    $this->set('time_range', isset($_GET['w']) ? $_GET['w'] : '');
    $this->set('current_user', isset($_GET['u']) ? $_GET['u'] : '');

    $this->set('vusers', $vusers);
    $this->set('data', $data);
  }

  function search($extra = null) {
    $this->autoRender = false;

    $dater = explode('-', $extra);
    $this->set('year', $dater[0]);
    $this->set('month', $dater[1]);
    $this->set('day', $dater[2]);

    $this->set('searchurlextra', $extra);

    $this->paginate = array(
      'order' => array(
        'Workorder.datetime_required ASC'
      ),

      'conditions' => array(
        'Workorder.location_id' => $this->Session->read('Auth.User.location_id'),
        'Workorder.status_id' => 1,
        'DATE(Workorder.created)' => $extra
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
    }

    if (!empty($this->data['Filters']['Department'])) {
      $this->paginate['conditions'][] = array('Department.id' => $this->data['Filters']['Department']);
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

    $this->set(compact(
      'ordertypes',
      'departments'
    ));

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

    $this->render('all');
  }

  function all($date = null, $to = null) {
    $dater = explode('-', $date);
    $this->set('month', $dater[1]);
    $this->set('day', $dater[2]);
    $this->set('year', $dater[0]);
    
    if ($to == true) {
      $this->paginate = array(
        'order' => array(
          'Workorder.datetime_required ASC',
          'Workorder.department_id ASC'
        ),
  
        'conditions' => array(
          'Workorder.location_id' => $this->Session->read('Auth.User.location_id'),
          'Workorder.status_id' => 1,
          'MONTH(Workorder.created)' => $dater[1],
          'YEAR(Workorder.created)' => $dater[0],
        ),
  
        'limit' => 2000,
      );
    } else {
      $this->paginate = array(
        'order' => array(
          'Workorder.datetime_required ASC'
        ),
  
        'conditions' => array(
          'Workorder.location_id' => $this->Session->read('Auth.User.location_id'),
          'Workorder.status_id' => 1,
          'DATE(Workorder.created)' => $date
        ),
  					
        'limit' => 50,
      );
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
    $this->set('searchurlextra', $date);

    $this->setData();

    $workorders = $this->paginate('Workorder');

    $this->set('workorders', $workorders);
    
    $fixedcosts = $this->FixedCost->find('all', array('conditions' => array('FixedCost.location_id' => $this->Session->read('Auth.User.location_id'))));
    $this->set('fixedcosts', $fixedcosts);
  }
}

?>

