<?php

class Admin_IndexController extends Zend_Controller_Action {

    public function init() {
        
    }

    public function indexAction() {

        $this->view->titulo = "modulo admin";

        $date = "1111/22/33";

        try {
            $dataAlt = "Helper de controller funcionou " . $this->_helper->util->reverseDate($date);
        } catch (Exception $ex) {
            $dataAlt = "Helper de controller NAO funcionou " . $ex;
        }
        $this->view->data = $dataAlt;
    }

}

