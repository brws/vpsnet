<?php
/**
*
*/
class AddonsController extends AppController {
  public $components = array('RequestHandler', 'Session');
  var $uses = array('Workorder', 'User', 'Department', 'Addon');

  function global_setup($id = null) {
    $this->setup($id, true);
  }

  function setup($id = null, $global = false) {

    if (!empty($this->data)) {
      foreach ($this->data['Addon'] as $parts) {
        if (empty($parts['id']) && !empty($parts['name'])) {
          $this->Addon->create();
        }

        $parts['location_id'] = $global == true ? 0 : $this->location['id'];

        if (!empty($parts['name'])) {
          $this->Addon->save($parts);
        } elseif (!empty($parts['id'])) {
          $parts['hidden'] = 1;
          $this->Addon->save($parts);
        }
      }

      $this->Session->setFlash('Addons Saved');

      if ($global == false)
        $this->redirect('/workorders/setup');
      else
        $this->redirect('/workorders/global_setup');
    }
  }

  function delete($id = null) {
    if (isset($id)) {
      $this->Addon->recursive = -1;
      $addon = $this->Addon->read(null, $id);
      $parts = $addon['Addon'];
      $parts['hidden'] = 1;
      $this->Addon->save($parts);

      $this->Session->setFlash('Addon Deleted');
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

