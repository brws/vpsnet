<?php

class OrdertypesController extends AppController {
  public $components = array('RequestHandler', 'Session');
  var $uses = array('Workorder', 'User', 'Department', 'Ordertype');

  function global_setup($id = null) {
    $this->setup($id, true);
  }

  function setup($id = null, $global = false) {

    if (!empty($this->data)) {
      foreach ($this->data['Ordertype'] as $parts) {
        if (empty($parts['id']) && !empty($parts['name'])) {
          $this->Ordertype->create();
        }

        $parts['location_id'] = $global == true ? 0 : $this->Session->read('Auth.User.location_id');

        if (!empty($parts['name'])) {
          $this->Ordertype->save($parts);
        } elseif (!empty($parts['id'])) {
          $parts['hidden'] = 1;
          $this->Ordertype->save($parts);
        }
      }

      $this->Session->setFlash('Work Orders Saved');

      if ($global == false)
        $this->redirect('/workorders/setup');
      else
        $this->redirect('/workorders/global_setup');
    }
  }

  function delete($id = null) {
    if (isset($id)) {
      $this->Ordertype->recursive = -1;
      $addon = $this->Ordertype->read(null, $id);
      $parts = $addon['Ordertype'];
      $parts['hidden'] = 1;
      $this->Ordertype->save($parts);

      $this->Session->setFlash('Work Order Deleted');
    }

    if (isset($_GET['global'])) {
      if ($_GET['global'] == true) {
        $this->redirect('/workorders/global_setup');
      } else {
        $this->redirect('/workorders/setup');
      }
    } else {
      $this->redirect('/workorders/setup');
    }
  }
}

