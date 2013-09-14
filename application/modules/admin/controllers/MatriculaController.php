<?php

/*
 * 
 * @author Cesar Augusto Vieira Giovani
 * @date 11/09/2013
 * 
 */

class Admin_MatriculaController extends Zend_Controller_Action {

    public function init() {
        $this->adpter = Zend_Db_Table_Abstract::getDefaultAdapter(); 
        $this->turmaDbTable = new Application_Model_DbTable_Turma();
        $this->treinamentoDbTable = new Application_Model_DbTable_Treinamento();
        $this->alunoDbTable = new Application_Model_DbTable_Aluno();
        $this->matriculaDbTable = new Application_Model_DbTable_Matricula();
        $this->view->menu = 'turma';
    }

    public function indexAction() {
        $this->view->titulo = "Listagem de Matrículas";
    }

    public function gridAction() {
        $this->getHelper('layout')->disableLayout();

        /* pega dados do filtro de pesquisa */
        $params['nome_treinamento'] = $this->_helper->util->utf8Decode($this->_helper->util->urldecodeGet($this->_getParam('nome_treinamento')));
        $params['tx_nome_aluno'] = $this->_helper->util->utf8Decode($this->_helper->util->urldecodeGet($this->_getParam('tx_nome_aluno')));
        $params['tx_cpf'] = $this->_helper->util->urldecodeGet($this->_getParam('tx_cpf'));        
        
        $params['data_inicial'] = $this->_helper->util->urldecodeGet($this->_getParam('data_inicial'));
        $params['data_inicial'] = trim($this->_helper->util->reverseDate($params['data_inicial']));

        $params['data_final'] = $this->_helper->util->urldecodeGet($this->_getParam('data_final'));
        $params['data_final'] = trim($this->_helper->util->reverseDate($params['data_final']));
                
        $matriculas = $this->matriculaDbTable->getDataGrid($params);

        $paginator = Zend_Paginator::factory($this->_helper->util->utf8Encode($matriculas));
                
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setDefaultItemCountPerPage(5);
        $this->view->paginator = $paginator;
    }   

    public function excluirAction() {
        $this->getHelper('viewRenderer')->setNoRender();
        $this->getHelper('layout')->disableLayout();

        try {
            $id = $this->getRequest()->getParam('id');
            $this->matriculaDbTable->delete("id_matricula = $id");

            $this->_helper->json->sendJson(array(
                'tipo' => 'sucesso',
                'msg' => 'Excluído com sucesso!',
                'url' => '/admin/matricula/index/'
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
    
    public function salvarAlunosAction() {
        $this->getHelper('viewRenderer')->setNoRender();
        $this->getHelper('layout')->disableLayout();
        
        $matricula = $this->_helper->util->utf8Decode($this->getRequest()->getPost('matricula')); 
        $alunos = $this->_helper->util->utf8Decode($this->getRequest()->getPost('alunos'));                 
                
        $this->adpter->beginTransaction();
        try {                             
            $id_turma = $matricula['id_turma'];
            $this->matriculaDbTable->delete("id_turma = $id_turma");
            
            $matricula['id_turma'] = $id_turma;
            foreach($alunos as $id_aluno) {
                $matricula['id_aluno'] = $id_aluno;
                $this->matriculaDbTable->insert($matricula);
            }                                 
            
            /** commita */
            $this->adpter->commit();                       
                        
            $this->_helper->json->sendJson(array(
                'tipo' => 'sucesso',
                'msg' => 'Alunos matriculados com sucesso!',
                'url' => '/admin/aluno/index/'
            ));
        } catch (Exception $exc) {
            if ($exc->getCode() == 23000) {
                $this->_helper->json->sendJson(array(
                    'tipo' => 'erro',
                    'msg' => "Esses registros possuem vínculos e não podem ser excluídos",
                ));
            } else {
                $this->_helper->json->sendJson(array(
                    'tipo' => 'erro',
                    'msg' => "Ocorreu um erro ao tentar executar a operacao, contate o administrador!" . $exc,
                ));
            }                                       
        }
              
    }
    
    public function matriculaAlunosAction() {
        $this->view->titulo = "Matrículas de Alunos em Turmas";
    }
    
    public function gridMatriculaGrupoAction()
    {
        $this->getHelper('layout')->disableLayout();
        
        $id_turma = $this->_getParam('id_turma');                                       
        
        $matriculas = $this->matriculaDbTable->getDataGrid(array('id_turma'=>$id_turma));                                                        
                        
        $this->view->matriculas = $this->_helper->util->utf8Encode($matriculas);                                  
    }
}

?>