<?php
class Car extends AppModel {
	var $name = 'Car';
	var $displayField = 'registration';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'Makemodel' => array(
			'className' => 'Makemodel',
			'foreignKey' => 'makemodel_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	var $hasMany = array(
		'Workorder' => array(
			'className' => 'Workorder',
			'foreignKey' => 'car_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

}
?>