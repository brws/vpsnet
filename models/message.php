<?php
class Message extends AppModel {
	var $name = 'Message';
	var $displayField = 'message';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
    'Workorder' => array(
			'className' => 'Workorder',
			'foreignKey' => 'workorder_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
  );

  function getFromWorkorder($id) {
    $messages = $this->find('all', array(
      'conditions' => array('Workorder.id' => $id),
      'order' => array('Message.id DESC')
    ));
      
    return $messages;
  }
}