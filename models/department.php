<?php
class Department extends AppModel {
	var $name = 'Department';
	var $displayField = 'name';

	var $belongsTo = array(
		'Location' => array(
			'className' => 'Location',
			'foreignKey' => 'location_id'
		)
	);

	var $hasMany = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'department_id'
		),
		'Workorder' => array(
			'className' => 'Workorder',
			'foreignKey' => 'department_id'
		)
	);

}
?>