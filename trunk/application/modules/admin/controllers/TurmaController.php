<?php

/*
 * 
 * @author raniela.carvalho
 * @date 03/09/2013
 * 
 */

class Admin_TurmaController extends Zend_Controller_Action {

    public function init() {
        $this->turmaDbTable = new Application_Model_DbTable_Turma();
        $this->treinamentoDbTable = new Application_Model_DbTable_Treinamento();
        $this->view->menu = 'turma';
    }

    public function indexAction() {
        $this->view->titulo = "Listagem de Turma";
    }

    public function gridAction() {
        $this->getHelper('layout')->disableLayout();


        $params['nome_treinamento'] = $this->_getParam('nome_treinamento');

        $params['data_inicial'] = $this->_helper->util->urldecodeGet($this->_getParam('data_inicial'));
        $params['data_inicial'] = trim($this->_helper->util->reverseDate($params['data_inicial']));

        $params['data_final'] = $this->_helper->util->urldecodeGet($this->_getParam('data_final'));
        $params['data_final'] = trim($this->_helper->util->reverseDate($params['data_final']));

        $turmas = $this->turmaDbTable->getDataGrid($params);

        $paginator = Zend_Paginator::factory($this->_helper->util->utf8Encode($turmas));
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setDefaultItemCountPerPage(5);
        $this->view->paginator = $paginator;
    }

    public function formAction() {

        //busca dados de treinamento para autocomplete e manda pra view
        $autoCompleteTreinamentos = $this->treinamentoDbTable->getAutoCompleteTreinamentos();
        $this->view->autoCompleteTreinamentos = $this->_helper->util->utf8Encode($autoCompleteTreinamentos);

        if ($this->getRequest()->getParam('id')) {

            /* edicao de treinamento */
            $this->view->titulo = "Edição de Turma";
            $id = $this->getRequest()->getParam('id');

            /* busca turma */
            $turma = $this->turmaDbTable->getDataGrid(array('id_venda' => $id));

            /* altera o formato das datas para popular o form */
            $turma[0]['dt_inicio_treinamento'] = $this->_helper->util->reverseDate($turma[0]['dt_inicio_treinamento']);
            $turma[0]['dt_termino_treinamento'] = $this->_helper->util->reverseDate($turma[0]['dt_termino_treinamento']);

            $this->view->turma = $this->_helper->util->utf8Encode($turma[0]);
        } else {
            $this->view->titulo = "Cadastro de Turma";
        }
    }

    public function salvarAction() {
        $this->getHelper('viewRenderer')->setNoRender();
        $this->getHelper('layout')->disableLayout();

        try {
            $turma = $this->_helper->util->utf8Decode($this->getRequest()->getPost());

            /* destroi o indice do vetor que não existe no banco */
            unset($turma['tx_nome_treinamento']);

            /* compara periodo de datas */
            $dt1 = new Zend_Date($turma['dt_inicio_treinamento']);
            $dt2 = new Zend_Date($turma['dt_termino_treinamento']);

            if ($dt1->isLater($dt2)) {
                $this->_helper->json->sendJson(array(
                    'tipo' => 'erro',
                    'msg' => "A data de início deve ser menor que a data de término!",
                ));
            }

            /* altera o formato das datas para salvar no banco */
            $turma['dt_inicio_treinamento'] = ($this->_helper->util->reverseDate($turma['dt_inicio_treinamento']));
            $turma['dt_termino_treinamento'] = ($this->_helper->util->reverseDate($turma['dt_termino_treinamento']));

            $id = null;
            if ($this->_getParam('id')) {
                $id = $this->_getParam('id');
            }

            if ($id != null) {
                $this->turmaDbTable->update($turma, "id_turma = {$id}");
            } else {
                $this->turmaDbTable->insert($turma);
            }

            $this->_helper->json->sendJson(array(
                'tipo' => 'sucesso',
                'msg' => 'Salvo com sucesso!',
                'url' => '/admin/turma/index/'
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
            $this->turmaDbTable->delete("id_turma = $id");

            $this->_helper->json->sendJson(array(
                'tipo' => 'sucesso',
                'msg' => 'Excluído com sucesso!',
                'url' => '/admin/turma/index/'
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