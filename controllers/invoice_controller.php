<?php
/**
*
*/
class InvoiceController extends AppController {
  public $components = array('RequestHandler', 'Session');
  var $uses = array('Invoice', 'Workorder', 'User', 'Role', 'FixedCost', 'VAT', 'Department', 'Ordertype');
  
  function index($m, $y) {
    $location_id = $this->location['id'];
    
    if (!empty($this->data)) {
      $charges = $this->data['Charges'];
          
      $dp = array();
      
      foreach ($this->data['Department'] as $dpi => $on) {
        if ($on == 1) $dp[] = $dpi;
      }
      
      $od = array();
      
      foreach ($this->data['Ordertype'] as $odi => $on) {
        if ($on == 1) $od[] = $odi;
      }
      
      $ch = array();
      
      foreach ($this->data['Charges'] as $data) {
        if (!empty($data['name'])) $ch[] = $data;
      }

      if (count($dp) == 0 && count($od) == 0) {
        $this->Session->setFlash("No deparments or ordertypes have been selected", null, 'error');
      } else {
        $invoice = array(
          'name' => $this->data['Invoice']['name'],
          'location_id' => $location_id,
          'month' => $m,
          'year' => $y,
          'ordertypes' => serialize($od),
          'departments' => serialize($dp),
          'charges' => serialize($ch),
          'show_charges' => $this->data['Invoice']['show_charges']
        );
        
        if ($this->Invoice->save($invoice)) {
          $this->data = array();
          $charges = array();
        } else {
          $this->Session->setFlash("Unable to save data", null, 'error');
        }
      }
    }

    $departments = $this->Department->find('all', array('conditions' => array('Department.hidden' => 0, 'or' => array(array('Department.location_id' => $location_id), array('Department.location_id' => 0))), 'recursive' => -1));
    $ordertypes = $this->Ordertype->find('all', array('conditions' => array('Ordertype.hidden' => 0, 'or' => array(array('Ordertype.location_id' => $location_id), array('Ordertype.location_id' => 0))), 'recursive' => -1));
    $invoices = $this->Invoice->find('all', array('conditions' => array('Invoice.location_id' => $location_id, 'Invoice.year' => $y, 'Invoice.month' => $m)));
    
    $this->set(compact('departments', 'ordertypes', 'invoices', 'charges'));
    $this->set('month', $m);
    $this->set('year', $y);
  }

  function delete_invoice($id) {
    if ($this->Invoice->delete($id)) {
      $this->redirect($this->referer());
    }
  }
  
  function print_invoice($m, $y = null) {
    if ($y == null) {
      $invoice_id = $m;
      $invoice = $this->Invoice->read(null, $invoice_id);
      $month = $invoice['Invoice']['month'];
      $year = $invoice['Invoice']['year'];
      $ordertypes = unserialize($invoice['Invoice']['ordertypes']);
      $departments = unserialize($invoice['Invoice']['departments']);
      $charges = unserialize($invoice['Invoice']['charges']);
      $name = $invoice['Invoice']['name'];
      $show_charges = $invoice['Invoice']['show_charges'];

      $this->invoice($month, $year, $ordertypes, $departments, $charges, $name, $show_charges); 
    } else {
      $this->invoice($m, $y);
    }
  }
  
  function invoice($month, $year, $ordertypes = null, $departments = null, $charges = null, $name = null, $show_charges = 0) {
    $this->layout = 'report';
    $this->autoRender = false;
    $location_id = $this->location['id'];
    $vat = $this->VAT->find('first', array('conditions' => array('VAT.id' => 1)));
    $end = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    
    if ($departments == null) {
      $_departments = $this->Department->find('list', array('conditions' => array('Department.hidden' => 0, 'or' => array(array('Department.location_id' => $location_id), array('Department.location_id' => 0))), 'recursive' => -1));
      $departments = array();
      foreach ($_departments as $deptid => $depts) {
        $departments[] = $deptid;
      }
    }

    if ($ordertypes == null) {
      $_ordertypes = $this->Ordertype->find('list', array('conditions' => array('Ordertype.hidden' => 0, 'or' => array(array('Ordertype.location_id' => $location_id), array('Ordertype.location_id' => 0))), 'recursive' => -1));
      $ordertypes = array();
      foreach ($_ordertypes as $ordid => $ordys) {
        $ordertypes[] = $ordid;
      }
    }
    
    if ($name == null) {
      $show_charges = 1;
      $charges = array();
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
    
    $fixedcosts = $this->FixedCost->find('all', array(
      'conditions' => array(
        'FixedCost.location_id' => $location_id,
        'FixedCost.hidden' => 0
      )
    ));
    
    $result = array();
    $result['Uncategorized'] = array();
    
    foreach ($departments as $department) {
      foreach ($workorders as $workorder) {
        if ($workorder['Workorder']['department_id'] == $department) {
          if (!isset($result[$workorder['Department']['name']])) {
            $result[$workorder['Department']['name']] = array();
          }
          
          $dept = $workorder['Department']['name'];
        
        
          if (!in_array($workorder['Workorder']['department_id'], $departments)) {
            $dept = 'Uncategorized';
          }
          
          foreach ($ordertypes as $ordertype) {
            if ($workorder['Ordertype'][0]['id'] == $ordertype) {
              if (!isset($result[$dept][$workorder['Ordertype'][0]['name']])) {
                $result[$dept][$workorder['Ordertype'][0]['name']] = array();
              }
              
              $result[$dept][$workorder['Ordertype'][0]['name']][] = $workorder;
            }
          }
        
        }
      }
    }

    if (count($result['Uncategorized']) == 0) {
      unset($result['Uncategorized']);
    }
    
    $total = array(
      'workorder_co' => 0,
      'workorder_ch' => 0,
      'fixedcost_co' => 0,
      'fixedcost_ch' => 0,
    );
    
    foreach ($result as $dept => $orty) {
      foreach ($orty as $iname => $works) {
                
        foreach ($works as $index => $work) {          
          $result[$dept][$iname][$index]['ordertype_co'] = 0;
          $result[$dept][$iname][$index]['ordertype_ch'] = 0;
          
          $result[$dept][$iname][$index]['addon_co'] = 0;
          $result[$dept][$iname][$index]['addon_ch'] = 0;
          
          foreach ($work['Ordertype'] as $ordertype) {
            $result[$dept][$iname][$index]['ordertype_co'] += (float) $ordertype['cost'];
            $result[$dept][$iname][$index]['ordertype_ch'] += (float) $ordertype['charge'];
          }
          
          foreach ($work['Addon'] as $addon) {
            $result[$dept][$iname][$index]['addon_co'] += (float) $addon['cost'];
            $result[$dept][$iname][$index]['addon_ch'] += (float) $addon['charge'];
          }
          
          $total['workorder_co'] += $result[$dept][$iname][$index]['ordertype_co'];
          $total['workorder_ch'] += $result[$dept][$iname][$index]['ordertype_ch'];
          
          $total['workorder_co'] += $result[$dept][$iname][$index]['addon_co'];
          $total['workorder_ch'] += $result[$dept][$iname][$index]['addon_ch'];
        }
      }
    }

    foreach ($charges as $charge) {
      $fixedcosts[] = array('FixedCost' => array(
        'id' => -1,
        'location_id' => $location_id,
        'name' => $charge['name'],
        'charge' => (float) $charge['value'],
        'cost' => 0,
        'order' => 0,
        'hidden' => 0,
        'timesperperiod' => 1,
        'period' => 'one-off'
      ));
    }

    $days_in_month = cal_days_in_month(0, $month, $year);
    $weeks_in_month = $days_in_month / 7;
    
    foreach ($fixedcosts as $index => $cost) {
      if ($fixedcosts[$index]['FixedCost']['period'] == 'weekly') {
        $fixedcosts[$index]['FixedCost']['charge'] = $fixedcosts[$index]['FixedCost']['charge'] * $weeks_in_month; 
        $fixedcosts[$index]['FixedCost']['cost'] = $fixedcosts[$index]['FixedCost']['cost'] * $weeks_in_month;
      }
      
      if ($fixedcosts[$index]['FixedCost']['period'] == 'daily') {
        $fixedcosts[$index]['FixedCost']['charge'] *= $days_in_month; 
        $fixedcosts[$index]['FixedCost']['cost'] *= $days_in_month;
      }

      $fixedcosts[$index]['FixedCost']['charge'] *= $fixedcosts[$index]['FixedCost']['timesperperiod'];
      $fixedcosts[$index]['FixedCost']['cost'] *= $fixedcosts[$index]['FixedCost']['timesperperiod'];
    }
    
    foreach ($fixedcosts as $fixedcost) {
      if (isset($fixedcost['FixedCost'])) {
        $total['fixedcost_co'] += $fixedcost['FixedCost']['cost'];
        $total['fixedcost_ch'] += $fixedcost['FixedCost']['charge'];
      }
    }

    $all = $total['workorder_ch'] + $total['fixedcost_ch'];
    $jwo = $total['workorder_ch'];
    
    $tvat = $vat;
    
    $vat = $vat['VAT']['value'] / 100;

    $total['vat_on_all'] = $all*$vat;
    $total['all_with_vat'] = ($all*$vat) + $all;
    
    $total['vat_on_jwo'] = $jwo*$vat;
    $total['jwo_with_vat'] = ($jwo*$vat) + $jwo;
    
    $this->set(array(
      'vat' => $tvat['VAT']['value'],
      'month' => $month,
      'year' => $year,
      'start' => 1,
      'end' => $end,
      'show_charges' => $show_charges,
      'invname' => $name == null ? 'Full Invoice' : $name,
      'result' => $result,
      'total' => $total,
      'fixedcosts' => $fixedcosts
    ));
    
    $this->render('print_invoice');
  }

  function preview_invoice($m, $y) {
    $month = $m; $year = $y; $start = 1;
        
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
    
    $name = $this->params['url']['name'];
    $show_charges = $this->params['url']['show_charges'];
    
    $this->invoice($month, $year, $ordertypes, $departments, $charges, $name, $show_charges);
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
          'Workorder.location_id' => $this->location['id'],
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
          'Workorder.location_id' => $this->location['id'],
          'Workorder.status_id' => 1,
          'DATE(Workorder.created)' => $date
        ),
        
        'limit' => 50,
      );
    }
    
    $this->User->recursive = -1;
    
    $users = $this->User->find('all', array(
      'conditions' => array(
        'User.location_id' => $this->location['id'],
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
    
    $fixedcosts = $this->FixedCost->find('all', array('conditions' => array('FixedCost.location_id' => $this->location['id'])));
    $this->set('fixedcosts', $fixedcosts);
    
    $vat = $this->VAT->find('first', array('conditions' => array('VAT.id' => 1))); 
    
    $this->set(array('vat' => $vat['VAT']['value']));
  }

  function setData() {
    $conditions = array(
      'conditions' => array(
        'or' => array(
          array('location_id' => $this->location['id']),
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

