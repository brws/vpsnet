<?php

class LocationHelper extends AppHelper {
  var $helpers = array('Session');
  var $uses = array('Location', 'Workorder');
  
  function beforeRender() {
    $location = $this->Session->read('Auth.User.location_id');
    $loverride = $this->Session->read('Loverride');
    
    if ($loverride && $location) {
      $loca = $this->Session->read('Loverride.Location');
      $this->location = $loca;
    } else {
      $loca = $this->Location->find('first', array('recursive' => 0, 'conditions' => array('Location.id' => $location)));
      // $this->Session->write('Loverride', $loca['Location']);
      // You can not write to a Session from the view
      $this->location = $loca['Location'];
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

