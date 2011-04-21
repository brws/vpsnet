<?php
/**
*
*/
class InvoiceController extends AppController {
  public $components = array('RequestHandler', 'Session');
  var $uses = array('Invoice', 'Workorder', 'User', 'Role', 'FixedCost', 'VAT', 'Department', 'Ordertype');
  
  function index($m, $y) {
    $location_id = $this->location['id'];
    $departments = $this->Department->find('all', array('conditions' => array('Department.hidden' => 0, 'or' => array(array('Department.location_id' => $location_id), array('Department.location_id' => 0))), 'recursive' => -1));
    $ordertypes = $this->Ordertype->find('all', array('conditions' => array('Ordertype.hidden' => 0, 'or' => array(array('Ordertype.location_id' => $location_id), array('Ordertype.location_id' => 0))), 'recursive' => -1));
    $invoices = $this->Invoice->find('all');
    
    $this->set(compact('departments', 'ordertypes', 'invoices'));
    $this->set('month', $m);
    $this->set('year', $y);
  }
  
  function print_invoice($m, $y = null) {
    $location_id = $this->location['id'];
    
    if ($y == null) {
      
    } else {
      
    }
  }
  
  function preview_invoice($m, $y) {
    $month = $m; $year = $y; $start = 1;
    $end = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    
    $ordertypes = $this->params['url']['ordertypes'];
    $departments = $this->params['url']['departments'];
    
    $charge_names = $this->params['url']['ccnames'];
    $charge_values = $this->params['url']['ccvalues'];
    
    $charges = array();
    
    foreach ($charge_names as $index => $charge_name) {
      if (strlen($charge_name) > 0) {
        $charges[$index] = array('name' => $charge_name, 'value' => $charge_values[$index]);
      }
    }
    
    $_departments = array();
    
    foreach ($departments as $department => $value) {
      if ($value == "true") {
        $_departments[] = $department;
      }
    }
    
    $departments = $_departments;
    
    $_ordertypes = array();
    
    foreach ($ordertypes as $ordertype => $value) {
      if ($value == "true") {
        $_ordertypes[] = $ordertype;
      }
    }
    
    $ordertypes = $_ordertypes;
    
    $location_id = $this->location['id'];
    
    if (count($departments) == 0 && count($ordertypes) == 0) {
      die("No deparments or ordertypes have been selected");
    }
    
    $workorders = $this->Workorder->find('all', array(
      'conditions' => array(
        'Workorder.location_id' => $location_id,
        'Workorder.status_id' => 1,
        'MONTH(Workorder.created)' => $month,
        'YEAR(Workorder.created)' => $year,
      ),
      'order' => array(
        'Workorder.datetime_required ASC',
      ),
    ));
    
    $result = array();
    $wc = 0;
    $af = array();
    
    foreach ($departments as $department) {
      foreach ($workorders as $workorder) {
        if ($workorder['Workorder']['department_id'] == $department) {
          if (!isset($result[$workorder['Department']['name']])) {
            $result[$workorder['Department']['name']] = array();
          }
          
          foreach ($ordertypes as $ordertype) {
            if ($workorder['Ordertype'][0]['id'] == $ordertype) {
              if (!isset($result[$workorder['Department']['name']][$workorder['Ordertype'][0]['name']])) {
                $result[$workorder['Department']['name']][$workorder['Ordertype'][0]['name']] = array();
              }
              
              $result[$workorder['Department']['name']][$workorder['Ordertype'][0]['name']][] = $workorder;
              $af[] = $workorder['Workorder']['id'];
              $wc++;
            }
          }
        }
      }
    }

    if ($wc !== count($workorders)) {
      $un = array();
      foreach ($workorders as $workorder) {
        $un[] = $workorder['Workorder']['id'];
      }
      
      $un = array_diff($un, $af);
      $this->set('uncategorized', $un);
    }

    $this->layout = 'report';
    $this->set(compact('month', 'year', 'start', 'end', 'results'));
  }
  
  function view($iid) {
    $location_id = $this->location['id'];
    
  }
  
  function create($m, $y) {
    $location_id = $this->location['id'];
    
  }
  
  function edit($iid) {
    $location_id = $this->location['id'];
    
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
    
        $vat = $this->VAT->find('first', array('conditions' => array('VAT.id' => 1))); 
    
    $this->set(array('vat' => $vat['VAT']['value']));
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
}

