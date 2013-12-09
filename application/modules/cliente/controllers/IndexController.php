<?php

class Cliente_IndexController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
        $this->view->titulo = "Módulo do Cliente";
    }

}

