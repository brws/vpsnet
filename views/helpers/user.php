<?php

class UserHelper extends AppHelper {
  var $helpers = array('Session');
  var $uses = array('Location');
  
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
  
  function getUser($field = null) {
    if ($field == null)
      return $this->output($this->Session->read('Auth.User.firstname') .' '. $this->Session->read('Auth.User.surname'));
      
    return $this->output($this->Session->read('Auth.User.' . $field));
  }
  
  function getLocation() {
    return $this->output($this->location['name']);
  }
  
  function logout() {
    return $this->output('(<a href="/users/logout" title="Log Out">Log Out</a>)');
  }
}