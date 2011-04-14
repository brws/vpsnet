<?php
/**
*
*/
class FixedCostsController extends AppController {
  public $components = array('RequestHandler', 'Session');
  var $uses = array('Workorder', 'User', 'Department', 'FixedCost');

  function global_setup($id = null) {
    $this->setup($id, true);
  }

  function setup($id = null, $global = false) {

    if (!empty($this->data)) {
      foreach ($this->data['FixedCost'] as $parts) {
        if (empty($parts['id']) && !empty($parts['name'])) {
          $this->FixedCost->create();
        }

        $parts['location_id'] = $global == true ? 0 : $this->location['id'];

        if (!empty($parts['name'])) {
          $this->FixedCost->save($parts);
        } elseif (!empty($parts['id'])) {
          $parts['hidden'] = 1;
          $this->FixedCost->save($parts);
        }
      }

      $this->Session->setFlash('Fixed Costs Saved');

      if ($global == false)
        $this->redirect('/workorders/setup');
      else
        $this->redirect('/workorders/global_setup');
    }
  }

  function delete($id = null) {
    if (isset($id)) {
      $this->FixedCost->recursive = -1;
      $fixedcost = $this->FixedCost->read(null, $id);
      $parts = $fixedcost['FixedCost'];
      $parts['hidden'] = 1;
      $this->FixedCost->save($parts);

      $this->Session->setFlash('Fixed Cost Deleted');
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

