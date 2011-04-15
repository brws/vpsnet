<?php

class UserHelper extends AppHelper {
  var $helpers = array('Session');
  var $uses = array('Location');
  
  function beforeRender() {
    $location = $this->Session->read('Auth.User.location_id');
    $loverride = $this->Session->read('Auth.Loverride');
    
    if (isset($loverride['id'])) {
      $this->location = $this->Session->read('Auth.Loverride');
    } else {
      if ($location > 0) {
        if ($this->Session->check('Locations.'.$location)) {
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