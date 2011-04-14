<?php
/**
* 
*/
class DepartmentsController extends AppController {
  public $components = array('RequestHandler', 'Session');
  var $uses = array('Workorder', 'User', 'Department');
  
  function global_setup($id = null) {
    $this->setup($id, true);
  }
  
  function setup($id = null, $global = false) {
    
    if (!empty($this->data)) {
      foreach ($this->data['Department'] as $parts) {
        if (empty($parts['id']) && !empty($parts['name'])) {
          $this->Department->create();
        }
        
        $parts['location_id'] = $global == true ? 0 : $this->location['id'];
        
        if (!empty($parts['name'])) {
          $this->Department->save($parts);
        } elseif (!empty($parts['id'])) {
          $parts['hidden'] = 1;
          $this->Department->save($parts);
        }
      }
      
      $this->Session->setFlash('Departments Saved');
      
      if ($global == false)
        $this->redirect('/workorders/setup');
      else
        $this->redirect('/workorders/global_setup');
    }
  }
  
  function delete($id = null) {
    if (isset($id)) {
      $this->Department->recursive = -1;
      $addon = $this->Department->read(null, $id);
      $parts = $addon['Department'];
      $parts['hidden'] = 1;
      $this->Department->save($parts);
      
      $this->Session->setFlash('Department Deleted');
    }
    
    if (isset($_GET['global'])) {
      $this->redirect('/workorders/global_setup');
    } else {
      $this->redirect('/workorders/setup');
    }
  }
}