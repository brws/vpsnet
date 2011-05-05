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
    $loverride = $this->Session->read('Auth.Loverride');
    
    if (isset($loverride['id'])) {
      $this->location = $this->Session->read('Auth.Loverride');
    } else {
      if ($location > 0) {
        if ($this->Session->check('Locations.'.$location)) {
          $user_loc = $this->Session->read('Locations.' . $location);
        } else {
          $this->Location->id = $location;
          $this->Location->recursive = -1;
          $user_loc = $this->Location->read();
          $this->Session->write('Locations.' . $location, $user_loc);
        }
        
        if ($user_loc) {
          $this->location = $user_loc['Location'];
        }
      }
    }
    
    if ($this->Session->check('Auth.User.id')) {
      $userid = $this->Session->read('Auth.User.id');
      $active = $this->User->read('active', $userid);
      
      if ($active['User']['active'] == 0) {
        $this->Session->del('Auth');
        $this->redirect('/users/logout', 301, true);
      }
    }
    
    $this->set('params', $this->params);
  }
}
?>
