<?php
/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.app
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       cake
 * @subpackage    cake.app
 */
class AppModel extends Model {

  public function find($type, $options = array()) { 
    if (!isset($options['joins'])) { 
      $options['joins'] = array(); 
    } 
    
    switch ($type) { 
      case 'matches': 
        if (!isset($options['model']) || !isset($options['scope'])) { 
          break; 
        }
        
        $assoc = $this->hasAndBelongsToMany[$options['model']]; 
        $bind = "{$assoc['with']}.{$assoc['foreignKey']} = {$this->alias}.{$this->primaryKey}"; 
      
        $options['joins'][] = array( 
          'table' => $assoc['joinTable'], 
          'alias' => $assoc['with'], 
          'type' => 'inner', 
          'foreignKey' => false, 
          'conditions'=> array($bind) 
        ); 
      
        $bind = $options['model'] . '.' . $this->{$options['model']}->primaryKey . ' = '; 
        $bind .= "{$assoc['with']}.{$assoc['associationForeignKey']}"; 
        
        $options['joins'][] = array( 
          'table' => $this->{$options['model']}->table, 
          'alias' => $options['model'], 
          'type' => 'inner', 
          'foreignKey' => false, 
          'conditions'=> array($bind) + (array) $options['scope']
        ); 
        
        unset($options['model'], $options['scope']);
        
        $type = 'all'; 
      break; 
    }
    
    return parent::find($type, $options); 
  }
}

class ModelSession {
  function read($str) {
    $vars = explode('.', $str);
    
    $arr = $_SESSION;
    
    foreach ($vars as $var) {
    
      while ($got = $this->findInArr($arr, $var)) {
        if (is_array($got) && !($var == $vars[count($vars)-1])) {
          $arr = $got;
        } else {
          return $got;
        }
      }
    }
  }
  
  private function findInArr($arr, $var) {
    foreach ($arr as $a => $b) {
      if ($a == $var) return $b;
    }
    return false;
  }
}

?>
