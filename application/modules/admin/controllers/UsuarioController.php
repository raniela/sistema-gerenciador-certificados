<?php

/*
 * 
 * @author raniela.carvalho
 * @date 16/08/2013
 * 
 */

class Admin_UsuarioController extends Zend_Controller_Action {

    public function indexAction() {
        $this->view->titulo = "Usuários";
    }
    
    public function gridAction() {
        
    }

    public function formAction() {
        $this->view->titulo = "Cadastro de Usuário";
    }
    
    public function salvarAction() {
        $this->getHelper('viewRenderer')->setNoRender();
        
        $data = $this->getRequest()->getPost();
        print_r($data); die;
    }
    
    
    public function excluirAction() {
        
    }
    
}

?>
