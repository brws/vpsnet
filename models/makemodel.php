<?php
class Makemodel extends AppModel {
	var $name = 'Makemodel';
	var $displayField = 'Model';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $hasMany = array(
		'Car' => array(
			'className' => 'Car',
			'foreignKey' => 'makemodel_id',
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
	
	function findModels($makes) {
	  return $this->find('list', array(
      'fields' => array('Makemodel.Model'),
      'order' => array('Makemodel.Model ASC'),
      'conditions' => array(
        'Makemodel.Make' => $makes,
        'Makemodel.Discontinued' => 0,
        'Makemodel.Enabled' => 1
      )
    ));
	}
	
	function findMakes($type = 1) {
	  $makes = $this->query(
			"SELECT DISTINCT `Makemodel`.`Make` AS `Make`
			FROM `makemodels` AS `Makemodel`
			WHERE `Makemodel`.`Discontinued` = 0 AND `Makemodel`.`Enabled` = 1
			AND `Makemodel`.`Type` = $type"
		);
		
		App::import('Set');
		
		$makes = Set::combine($makes, '{n}.Makemodel.Make', '{n}.Makemodel.Make');
		return $makes;
	}

}
?>