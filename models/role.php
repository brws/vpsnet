<?php
class Role extends AppModel {
	var $name = 'Role';
	var $displayField = 'name';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $hasMany = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'role_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
	);
	
	function lookupRole($user_id) {
	  $user = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
	  return $user['Role'];
	}
	
  function is($uid, $rid = null) {
    $this->Session = new ModelSession();
  
    if ($rid == null) {
      $rid = $uid;
      $uid = $this->Session->read('Auth.User.id');
      $role = array();
      $role['id'] = $this->Session->read('Auth.User.role_id');
    } else {
      $role = $this->lookupRole($uid);
    }
  
    if ($role['id'] == $rid) return true;
    return false;
  }
  
  function atleast($uid, $rid = null) {
    $this->Session = new ModelSession();
    
    if ($rid == null) {
      $rid = $uid;
      $uid = $this->Session->read('Auth.User.id');
      $role = array();
      $role['id'] = $this->Session->read('Auth.User.role_id');
    } else {
      $role = $this->Role->lookupRole($uid);
    }
    
    $check = array_search($rid, $this->ckorder);
    $user = array_search($role['id'], $this->ckorder);
    
    if ($user <= $check) return true;
    return false;
  }
	
	public $SUPER = 1;
	public $DEALER_ADMIN = 2;
	public $DEALER_STAFF = 3;
	public $VALET_ADMIN = 4;
	public $VALET_STAFF = 5;
	
	private $ckorder = array(1, 4, 2, 3, 5);

}
?>
