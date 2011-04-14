<?php
/**
 * Short description for file.
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
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
 * Short description for class.
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       cake
 * @subpackage    cake.app
 */

class AppController extends Controller {
  public $view = 'Dwoo';
  public $components = array('Auth', 'Session');
  public $helpers = array('User', 'Location', 'Session', 'Form', 'Role');
  public $uses = array('Location');
  public $location;
  
  function beforeFilter() {
    $location = $this->Session->read('Auth.User.location_id');
    $loverride = $this->Session->read('Loverride');
    
    if ($loverride && $location) {
      $loca = $this->Session->read('Loverride.Location');
      $this->location = $loca;
    } else {
      $loca = $this->Location->find('first', array('recursive' => 0, 'conditions' => array('Location.id' => $location)));
      $this->Session->write('Loverride', $loca['Location']);
      $this->location = $loca['Location'];
    }
    
    $this->set('params', $this->params);
  }
}
?>
