<?php
class Workorder extends AppModel {
	var $name = 'Workorder';
	var $displayField = 'id';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'Car' => array(
			'className' => 'Car',
			'foreignKey' => 'car_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'AuthorisedByUser' => array(
			'className' => 'User',
			'foreignKey' => 'authorised_by_user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'AssignedToUser' => array(
			'className' => 'User',
			'foreignKey' => 'assigned_to_user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'UpdatedByUser' => array(
			'className' => 'User',
			'foreignKey' => 'updated_by',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'CompletedByUser' => array(
			'className' => 'User',
			'foreignKey' => 'completed_by',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Department' => array(
			'className' => 'Department',
			'foreignKey' => 'department_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Status' => array(
			'className' => 'Status',
			'foreignKey' => 'status_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Location' => array(
			'className' => 'Location',
			'foreignKey' => 'location_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	var $hasAndBelongsToMany = array(
		'Addon' => array(
			'className' => 'Addon',
			'joinTable' => 'addons_workorders',
			'foreignKey' => 'workorder_id',
			'associationForeignKey' => 'addon_id',
			'unique' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		),

		'Ordertype' => array(
			'className' => 'Ordertype',
			'joinTable' => 'ordertypes_workorders',
			'foreignKey' => 'workorder_id',
			'associationForeignKey' => 'ordertype_id',
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

