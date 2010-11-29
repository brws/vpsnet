<?php

  class UsersController extends AppController {
    var $components = array('Session');

    function beforeFilter() {
      parent::beforeFilter();
      $this->Auth->authError = "Please provide your login details to access VPSNet";
      $this->Auth->autoRedirect = false;
      $this->Auth->loginRedirect = array('controller' => 'workorders', 'action' => 'index');
      $this->Auth->userScope = array('User.active' => true);
    }

    function login() {
      $this->layout = 'login';

      $dealer = explode('.', $_SERVER['HTTP_HOST']);
      $dealer = $dealer[0];

      $this->Location->recursive = -1;
      $location = $this->Location->find('first', array('conditions' => array('Location.url' => $dealer)));

      $this->User->recursive = -1;
      $users = $this->User->find('all', array('conditions' => array('User.active' => 1, 'User.location_id' => $location['Location']['id'])));
      $this->set('users', $users);

      if (!empty($this->data) && $this->Auth->user()) {
        $user = $this->Auth->user();
        $this->redirect($this->Auth->redirect());
      }
    }

    function logout() {
      $this->redirect($this->Auth->logout());
    }

    function edit() {
      if (!empty($this->data)) {
        $find = $this->User->find('first', array('conditions' => array('User.id' => $this->data['User']['id'])));

        if (!empty($find)) {
          $this->data['User']['username'] = $find['User']['username'];
          $this->data['User']['location_id'] = $find['User']['location_id'];

          if (!empty($this->data['User']['password'])) {
            $this->data['User']['password'] = sha1($this->data['User']['password']);
          } else {
            $this->data['User']['password'] = $find['User']['password'];
          }

          if (is_array($this->data['User']['role_id'])) {
            $this->data['User']['role_id'] = $this->data['User']['role_id'][0];
          }

          if ($this->User->save($this->data['User'])) {
            $this->Session->setFlash('Details saved');
            $this->redirect('/settings');
            exit;
          } else {
            $this->Session->setFlash('Unable to save User');
            $this->redirect('/settings');
            exit;
          }
        } else {
          $this->Session->setFlash('User not found');
          $this->redirect('/settings');
          exit;
        }
      }
    }

    function add() {
      if (!empty($this->data)) {
        $loc = $this->Session->read('Auth.User.location_id');
        $username = $this->data['User']['firstname'][0] . $this->data['User']['surname'][0] . $loc;

        $find = $this->User->find('first', array('conditions' => array('User.username' => $username)));

        while (!empty($find)) {

          if (substr($find['User']['username'], -2, 1) == '_') {
            $username = substr($find['User']['username'],0, -1) . (((int) substr($find['User']['username'], -1, 1)) + 1);
          } else {
            $username = $find['User']['username'] . '_1';
          }

          $find = $this->User->find('first', array('conditions' => array('User.username' => $username)));
        }

        $this->data['User']['password'] = sha1($this->data['User']['password']);
        $this->data['User']['username'] = strtolower($username);
        $this->data['User']['location_id'] = $loc;
        $this->data['User']['role_id'] = $this->data['User']['role_id'][0];

        $this->User->create();

        if ($this->User->save($this->data['User'])) {
          $this->Session->setFlash('User added');
        } else {
          $this->Session->setFlash('User not added. Contact System Administrator.');
          debug($this->data);
          debug($this->User->invalidFields());
        }

        $this->redirect('/settings');
        exit;
      }
    }
  }

?>

