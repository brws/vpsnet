<?php

class SettingsController extends AppController {
  var $uses = array('Location', 'User', 'Department', 'Role');
  var $components = array('Session', 'RequestHandler');
  public $helpers = array('Ajax', 'Js' => array('Prototype'), 'Paginator');

  function index() {
    if ($this->RequestHandler->isAjax()) {
      $this->layout = 'ajax';
    }
    
    $location = array('Location' => $this->location);

    $user = $this->User->find('first', array('conditions' => array(
      'User.id' => $this->Session->read('Auth.User.id'))
    ));

    $users = $this->User->find('all', array('conditions' => array('User.active' => isset($this->data['deactivated']) ? $this->data['deactivated'] == 1 ? 0 : 1 : 1, 'User.location_id' => $this->location['id'])));
    $deac = $this->data['deactivated'];
    $this->data = array_merge($location, $user);
    $this->data['Location'] = $this->location;
    $this->data['deactivated'] = $deac;
    
    $data = $this->data;

    $departments = $this->Department->find('list', array(
      'conditions' => array(
        'Department.hidden' => 0,
        'or' => array(
          'Department.location_id' => $this->location['id'],
          'Department.location_id' => 0
        )
      )
    ));

    if ($this->Role->is($this->Role->SUPER)) {
      $roles = $this->Role->find('list');
    } else {
      $roles = $this->Role->find('list', array('conditions' => array('Role.id > ' => 1)));
    }

    $this->set(compact('data', 'departments', 'users', 'roles'));
  }

  function edit($id = null) {
    $this->autoRender = false;
    $this->layout = 'ajax';
    $departments = $this->Department->find('list', array(
      'conditions' => array(
        'Department.hidden' => 0,
        'or' => array(
          'Department.location_id' => $this->location['id'],
          'Department.location_id' => 0
        )
      )
    ));

    if ($this->Role->is($this->Role->SUPER)) {
      $roles = $this->Role->find('list');
    } else {
      $roles = $this->Role->find('list', array('conditions' => array('Role.id > ' => 1)));
    }

    $data = $this->User->find('first', array('conditions' => array('User.id' => $id)));
    $this->data = $data;

    $this->set(compact('departments', 'roles', 'data'));
    $this->render('ajax/user_edit');
  }

  function add() {
    $this->autoRender = false;
    $this->layout = 'ajax';
    $departments = $this->Department->find('list', array(
      'conditions' => array(
        'Department.hidden' => 0,
        'or' => array(
          'Department.location_id' => $this->location['id'],
          'Department.location_id' => 0
        )
      )
    ));

    if ($this->Role->is($this->Role->SUPER)) {
      $roles = $this->Role->find('list');
    } else {
      $roles = $this->Role->find('list', array('conditions' => array('Role.id > ' => 1)));
    }

    $this->set(compact('departments', 'roles'));
    $this->render('ajax/user');
  }
}

?>

