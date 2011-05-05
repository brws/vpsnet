<?php

class LocationHelper extends AppHelper {
  var $helpers = array('Session');
  var $uses = array('Location', 'Workorder');
  
  function beforeRender() {
    $location = $this->Session->read('Auth.User.location_id');
    $loverride = $this->Session->read('Auth.Loverride');
    
    if (isset($loverride['id'])) {
      $this->location = $this->Session->read('Auth.Loverride');
    } else {
      if ($location > 0) {
        if ($this->Session->check('Locations.' . $location)) {
          $user_loc = $this->Session->read('Locations.' . $location);
        } else {
          $this->Location->id = $location;
          $this->Location->recursive = -1;
          $user_loc = $this->Location->read();
        }
        
        if ($user_loc) {
          $this->location = $user_loc['Location'];
        }
      }
    }
  }

  function getJobsCountNow() {
    $workorders = $this->Workorder->find('count', array(
      'conditions' => array(
        'location_id' => $this->location['id'],
        array('status_id' => 3) // in progress only
      ),
      'recursive' => -1
    ));

    return $workorders;
  }

  function getJobsCountToday() {
    $workorders = $this->Workorder->find('count', array(
      'conditions' => array(
        'location_id' => $this->location['id'],
        'DAY(datetime_required) <=' => date('j', strtotime('now')),
        'MONTH(datetime_required) <=' => date('n', strtotime('now')),
        'YEAR(datetime_required) <=' => date('Y', strtotime('now')),
        'and' => array(
          array('status_id NOT' => 1), // completed
          array('status_id NOT' => 2) // cancelled
        )
      ),
      'recursive' => -1
    ));

    return $workorders;
  }

  function getJobsCountTomorrow() {
    $workorders = $this->Workorder->find('count', array(
      'conditions' => array(
        'location_id' => $this->location['id'],
        'DAY(datetime_required) =' => date('j', strtotime('+1 day')),
        'MONTH(datetime_required) =' => date('n', strtotime('+1 day')),
        'YEAR(datetime_required) =' => date('Y', strtotime('+1 day')),
        'and' => array(
           array('status_id NOT' => 1), // completed
          array('status_id NOT' => 2) // cancelled
         )
      ),
      'recursive' => -1
    ));

    return $workorders;
  }
}

