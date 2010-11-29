<?php

class UserHelper extends AppHelper {
  var $helpers = array('Session');
  var $uses = array('Location');
  
  function getUser($field = null) {
    if ($field == null)
      return $this->output($this->Session->read('Auth.User.firstname') .' '. $this->Session->read('Auth.User.surname'));
      
    return $this->output($this->Session->read('Auth.User.' . $field));
  }
  
  function getLocation() {
    return $this->output($this->Session->read('Location.name'));
  }
  
  function logout() {
    return $this->output('(<a href="/users/logout" title="Log Out">Log Out</a>)');
  }
}