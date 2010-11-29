<?php

class LocationHelper extends AppHelper {
  var $helpers = array('Session');
  var $uses = array('Location', 'Workorder');

  function getJobsCountNow() {
    $workorders = $this->Workorder->find('count', array(
      'conditions' => array(
        'location_id' => $this->Session->read('Auth.User.location_id'),
        array('status_id' => 3) // in progress only
      ),
      'recursive' => -1
    ));

    return $workorders;
  }

  function getJobsCountToday() {
    $workorders = $this->Workorder->find('count', array(
      'conditions' => array(
        'location_id' => $this->Session->read('Auth.User.location_id'),
        'DAY(datetime_required)' => date('j', strtotime('now')),
        'MONTH(datetime_required)' => date('n', strtotime('now')),
        'YEAR(datetime_required)' => date('Y', strtotime('now')),
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
        'location_id' => $this->Session->read('Auth.User.location_id'),
        'DAY(datetime_required)' => date('j', strtotime('+1 day')),
        'MONTH(datetime_required)' => date('n', strtotime('+1 day')),
        'YEAR(datetime_required)' => date('Y', strtotime('+1 day')),
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

