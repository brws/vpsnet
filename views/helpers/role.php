<?php

class RoleHelper extends AppHelper {
  var $helpers = array('Session');
  var $uses = array('Role');
  
  function is($uid, $rid = null) {
    if ($rid == null) {
      $rid = $uid;
      $uid = $this->Session->read('Auth.User.id');
      $role = array();
      $role['id'] = $this->Session->read('Auth.User.role_id');
    } else {
      $role = $this->Role->lookupRole($uid);
    }
  
    if ($role['id'] == $rid) return true;
    return false;
  }
  
  function atleast($uid, $rid = null) {
    if ($rid == null) {
      $rid = $uid;
      $uid = $this->Session->read('Auth.User.id');
      $role = array();
      $role['id'] = $this->Session->read('Auth.User.role_id');
    } else {
      $role = $this->Role->lookupRole($uid);
    }
    
    $check = array_search($rid, $this->order);
    $user = array_search($role['id'], $this->order);
    
    if ($user <= $check) return true;
    return false;
  }
  
	public $SUPER = 1;
	public $DEALER_ADMIN = 2;
	public $DEALER_STAFF = 3;
	public $VALET_ADMIN = 4;
	public $VALET_STAFF = 5;
	
	private $order = array(1, 4, 2, 3, 5);
}
