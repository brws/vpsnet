<?php
class Ordertype extends AppModel {
	var $name = 'Ordertype';
	var $displayField = 'name';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'Location' => array(
			'className' => 'Location',
			'foreignKey' => 'location_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	var $hasAndBelongsToMany = array(
		'Workorder' => array(
			'className' => 'Workorder',
			'joinTable' => 'ordertypes_workorders',
			'foreignKey' => 'ordertype_id',
			'associationForeignKey' => 'workorder_id',
			'unique' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		)
	);

}
?>