<?php

/*
 * 
 * @author raniela.carvalho
 * @date 16/08/2013
 * 
 */

class Admin_UsuarioController extends Zend_Controller_Action {

    private $flashMessenger;

    public function init() {
        $this->flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->msg = $this->flashMessenger->getMessages();
    }

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
        $this->getHelper('layout')->disableLayout();

        $this->flashMessenger->addMessage('Salvo com sucesso!');

        $this->_helper->json->sendJson(array(
            'tipo' => 'sucesso',
            'msg' => 'Salvo com sucesso!',
            'url' => '/admin/usuario/index/'
        ));
    }

    public function excluirAction() {
        
    }

}

?>
