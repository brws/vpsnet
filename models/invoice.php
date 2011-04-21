<?php
class Invoice extends AppModel {
  var $name = 'Invoice';

  var $belongsTo = array(
    'Location' => array(
      'className' => 'Location',
      'foreignKey' => 'location_id',
      'conditions' => '',
      'fields' => '',
      'order' => ''
    )
  );
}
?>