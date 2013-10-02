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
        $this->view->titulo = "Listagem de Treinamentos";
    }

    public function gridAction() {
        $this->getHelper('layout')->disableLayout();

        $nome_treinamento = $this->_getParam('nome_treinamento');
        $nome_instrutor = $this->_getParam('nome_instrutor');

        $select = $this->treinamentoDbTable->select();
        //print_r($nome . $tipo_usuario); die;

        if (!empty($nome_treinamento)) {
            $select->where("tx_nome_treinamento LIKE ?", "%$nome_treinamento%");
        }
        if (!empty($nome_instrutor)) {
            $select->where("tx_nome_instrutor LIKE ?", "%$nome_instrutor%");
        }

        $select->order('tx_nome_instrutor');

        $treinamentos = $this->_helper->util->utf8Encode($select->query()->fetchAll());

        //print_r($usuarios); die;

        $paginator = Zend_Paginator::factory($treinamentos);
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setDefaultItemCountPerPage(5);
        $this->view->paginator = $paginator;
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

        try {
            $treinamento = $this->_helper->util->utf8Decode($this->getRequest()->getPost());

            //print_r($treinamento); die;

            $id = null;
            if ($this->_getParam('id')) {
                $id = $this->_getParam('id');
            }

            if ($id != null) {
                $this->treinamentoDbTable->update($treinamento, "id_treinamento = {$id}");
            } else {
                $this->treinamentoDbTable->insert($treinamento);
            }

            $this->_helper->json->sendJson(array(
                'tipo' => 'sucesso',
                'msg' => 'Salvo com sucesso!',
                'url' => '/admin/treinamento/index/'
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
            $this->treinamentoDbTable->delete("id_treinamento = $id");

            $this->_helper->json->sendJson(array(
                'tipo' => 'sucesso',
                'msg' => 'Excluído com sucesso!',
                'url' => '/admin/treinamento/index/'
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

    public function pesquisarTreinamentoAction() {
        try {
            $this->getHelper('layout')->disableLayout();

            $params = $this->_getAllParams();
            $params = $this->_helper->util->urldecodeGet($params);
            $params = $this->_helper->util->utf8Decode($params);
            $params['limit'] = 5;

            
            $this->view->dataGrid = $this->_helper->util->utf8Encode($this->treinamentoDbTable->getDataGrid($params));

            //print_r($this->view->dataGrid);
            //die();
        } catch (Exception $e) {
            echo $e->getMessage();
            die('ERRO|Ocorreu um erro ao tentar executar a operação. Tente novamente. Caso persista, contate o administrador do sistema.');
        }
    }

}

?>
