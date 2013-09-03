<?php

/*
 * 
 * @author raniela.carvalho
 * @date 02/09/2013
 * 
 */

class Admin_TreinamentoController extends Zend_Controller_Action {

    public function init() {
        $this->treinamentoDbTable = new Application_Model_DbTable_Treinamento();
        $this->view->menu = 'treinamento';
    }

    public function indexAction() {
        $this->view->titulo = "Cadastro de Treinamentos";
    }

    public function gridAction() {
        $this->getHelper('layout')->disableLayout();
//        $nome = $this->_getParam('nome');
//        $tipo_usuario = $this->_getParam('tipo_usuario');
//
//        $select = $this->usuarioDbTable->select();
//        //print_r($nome . $tipo_usuario); die;
//
//        if (!empty($nome)) {
//            $select->where("tx_nome LIKE ?", "%$nome%");
//        }
//        if (!empty($tipo_usuario)) {
//            $select->where("tx_tipo_usuario LIKE ?", "%$tipo_usuario%");
//        }
//
//        $select->order('tx_nome');
//
//        $usuarios = $this->_helper->util->utf8Encode($select->query()->fetchAll());
//
//        //print_r($usuarios); die;
//
//        $paginator = Zend_Paginator::factory($usuarios);
//        $paginator->setCurrentPageNumber($this->_getParam('page'));
//        $paginator->setDefaultItemCountPerPage(5);
//        $this->view->paginator = $paginator;
    }

    public function formAction() {

        if ($this->getRequest()->getParam('id')) {

            /* edicao de treinamento */
            $this->view->titulo = "Edição de Treinamento";
            $id = $this->getRequest()->getParam('id');

            /* busca treinamento */
            $treinamento = $this->treinamentoDbTable->fetchRow("id_treinamento = '{$id}'")->toArray();

            $this->view->treinamento = $this->_helper->util->utf8Encode($treinamento);
        } else {
            $this->view->titulo = "Cadastro de Treinamento";
        }
    }

    public function salvarAction() {
        $this->getHelper('viewRenderer')->setNoRender();
        $this->getHelper('layout')->disableLayout();
//
//        try {
//            $usuario = $this->getRequest()->getPost();
//
//            //print_r($usuario); die;
//
//            $usuario['tx_tipo_usuario'] = USUARIO_ADMIN;
//
//            $id = null;
//            if ($this->_getParam('id')) {
//                $id = $this->_getParam('id');
//            }
//
//            if ($this->usuarioDbTable->verificaDb($id, $usuario) == false) {
//                $this->_helper->json->sendJson(array(
//                    'tipo' => 'erro',
//                    'msg' => 'Usuário já existente com esse Login'
//                ));
//            }
//
//            if ($id != null) {
//                $this->usuarioDbTable->update($usuario, "id_usuario = {$id}");
//            } else {
//                $this->usuarioDbTable->insert($usuario);
//            }
//
//            $this->_helper->json->sendJson(array(
//                'tipo' => 'sucesso',
//                'msg' => 'Salvo com sucesso!',
//                'url' => '/admin/usuario/index/'
//            ));
//        } catch (Exception $exc) {
//            $this->_helper->json->sendJson(array(
//                'tipo' => 'erro',
//                'msg' => "Ocorreu um erro ao tentar executar a operacao, contate o administrador!" . $exc,
//            ));
//        }
    }

    public function excluirAction() {
        $this->getHelper('viewRenderer')->setNoRender();
        $this->getHelper('layout')->disableLayout();

//        try {
//            $id = $this->getRequest()->getParam('id');
//            $this->usuarioDbTable->delete("id_usuario = $id");
//
//            $this->_helper->json->sendJson(array(
//                'tipo' => 'sucesso',
//                'msg' => 'Excluído com sucesso!',
//                'url' => '/admin/usuario/index/'
//            ));
//        } catch (Exception $exc) {
//
//            if ($exc->getCode() == 23000) {
//                $this->_helper->json->sendJson(array(
//                    'tipo' => 'erro',
//                    'msg' => "Esse registro possui vínculos e não pode ser excluído",
//                ));
//            } else {
//                $this->_helper->json->sendJson(array(
//                    'tipo' => 'erro',
//                    'msg' => "Ocorreu um erro ao tentar executar a operacao, contate o administrador!" . $exc,
//                ));
//            }
//        }
    }

}

?>
