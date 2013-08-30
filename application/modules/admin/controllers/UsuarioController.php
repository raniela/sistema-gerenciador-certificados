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
        $this->usuarioDbTable = new Application_Model_DbTable_Usuario();
        $this->flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->msg = $this->flashMessenger->getMessages();
    }

    public function indexAction() {
        $this->view->titulo = "Cadastro de Usuário";
    }

    public function gridAction() {
        $this->getHelper('layout')->disableLayout();
        $nome = $this->_getParam('nome');
        $tipo_usuario = $this->_getParam('tipo_usuario');

        $select = $this->usuarioDbTable->select();
        //print_r($nome . $tipo_usuario); die;

        if (!empty($nome)) {
            $select->where("tx_nome LIKE ?", "%$nome%");
        }
        if (!empty($tipo_usuario)) {
            $select->where("tx_tipo_usuario LIKE ?", "%$tipo_usuario%");
        }

        $select->order('tx_nome');

        $usuarios = $this->_helper->util->utf8Encode($select->query()->fetchAll());

        //print_r($usuarios); die;

        $paginator = Zend_Paginator::factory($usuarios);
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setDefaultItemCountPerPage(5);
        $this->view->paginator = $paginator;
    }

    public function formAction() {

        if ($this->getRequest()->getParam('id')) {

            /* edicao de usuario */
            $this->view->titulo = "Edição de Usuário";
            $id = $this->getRequest()->getParam('id');

            /* busca usuario */
            $usuario = $this->usuarioDbTable->fetchRow("id_usuario = '{$id}'")->toArray();

            if ($usuario['tx_tipo_usuario'] == 1) {
                $usuario['tx_tipo_usuario'] = "Administrador";
            } elseif ($usuario['tx_tipo_usuario'] == 2) {
                $usuario['tx_tipo_usuario'] = "Cliente";
            } elseif ($usuario['tx_tipo_usuario'] == 3) {
                $usuario['tx_tipo_usuario'] = "Aluno";
            }

            $this->view->usuario = $this->_helper->util->utf8Encode($usuario);
        } else {
            $this->view->titulo = "Cadastro de Usuário";
        }
    }

    public function salvarAction() {
        $this->getHelper('viewRenderer')->setNoRender();
        $this->getHelper('layout')->disableLayout();

        try {
            $usuario = $this->getRequest()->getPost();

            //print_r($usuario); die;

            $usuario['tx_tipo_usuario'] = USUARIO_ADMIN;

            /* if ($this->usuarioDbTable->verificaDb($id, $usuario) == false) {
              $this->_helper->json->sendJson(array(
              'tipo' => 'erro',
              'url' => '/index/tabs/dir/2/',
              'msg' => 'Usuário já existente com esse Login'
              ));
              } */


            if ($this->_getParam('id')) {
                $id = $this->_getParam('id');
                $this->usuarioDbTable->update($usuario, "id_usuario = {$id}");
            } else {
                $this->usuarioDbTable->insert($usuario);
            }

            $this->_helper->json->sendJson(array(
                'tipo' => 'sucesso',
                'msg' => 'Salvo com sucesso!',
                'url' => '/admin/usuario/index/'
            ));
        } catch (Exception $exc) {
            $this->_helper->json->sendJson(array(
                'tipo' => 'erro',
                'msg' => "Ocorreu um erro ao tentar executar a operacao, contate o administrador!" . $exc,
            ));
        }
    }

    public function excluirAction() {
        $this->getHelper('viewRenderer')->setNoRender();
        $this->getHelper('layout')->disableLayout();

        try {
            $id = $this->getRequest()->getParam('id');
            $this->usuarioDbTable->delete("id_usuario = $id");
            
            $this->_helper->json->sendJson(array(
                'tipo' => 'sucesso',
                'msg' => 'Excluído com sucesso!',
                'url' => '/admin/usuario/index/'
            ));
        } catch (Exception $exc) {

            if ($exc->getCode() == 23000) {
                $this->_helper->json->sendJson(array(
                    'tipo' => 'erro',
                    'msg' => "Esse registro possui vínculos e não pode ser excluído",
                ));
            } else {
                $this->_helper->json->sendJson(array(
                    'tipo' => 'erro',
                    'msg' => "Ocorreu um erro ao tentar executar a operacao, contate o administrador!" . $exc,
                ));
            }
        }
    }

}

?>
