<?php
/**
* 
*/
class LocationsController extends AppController {
  public $components = array('RequestHandler', 'Session');
  var $uses = array('Workorder', 'User');
  
  function index($id = null) {
    if (!empty($this->data)) {
      if ($this->Location->saveAll($this->data)) {
        $this->Session->setFlash('Business Details Saved');
        $this->redirect('/settings');
        exit;
      } else {
        $this->Session->setFlash('Unable to save Business Details');
        $this->redirect('/settings');
        exit;
      }
    }
  }
}