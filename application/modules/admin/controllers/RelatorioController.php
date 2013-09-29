<?php
/*
 * 
 * @author Cesar Augusto Vieira Giovani
 * @date 29/09/2013
 * 
 */
class Admin_RelatorioController extends Zend_Controller_Action {
    
    public function init() {                                                        
        $this->certificadoEmitidoDbTable = new Application_Model_DbTable_CertificadoEmitido();        
        $this->matriculaDbTable = new Application_Model_DbTable_Matricula();
        $this->turmaDbTable = new Application_Model_DbTable_Turma();
        $this->alunoDbTable = new Application_Model_DbTable_Aluno();
        
        $this->view->menu = 'relatorio';
    }


    public function filtroRelatorioTreinamentosAction() {
        $this->view->titulo = "Relatório de Treinamentos Realizados";
    }
    
    public function relatorioTreinamentosAction() {
        //desabilita layout
        $this->getHelper('layout')->disableLayout();
        
        $params = $this->_helper->util->utf8Decode($this->getAllParams());
        
        $params['data_inicial'] = $this->_helper->util->urldecodeGet($this->_getParam('data_inicial'));
        $params['data_inicial'] = trim($this->_helper->util->reverseDate($params['data_inicial']));

        $params['data_final'] = $this->_helper->util->urldecodeGet($this->_getParam('data_final'));
        $params['data_final'] = trim($this->_helper->util->reverseDate($params['data_final']));
        
        $turmas = $this->turmaDbTable->getDataGrid($params);
        
        foreach ($turmas as $indice => $turma) {
            $alunos = $this->matriculaDbTable->getAlunosToRelTreinamentos(array('id_turma'=>$turma['id_turma']));            
            $turmas[$indice]['alunos'] = $alunos;
        }
        
        $this->view->turmas = $turmas;
//        echo "<pre>";
//        print_r($this->view->turmas);
//        die();
    }
    
    public function filtroRelatorioCertificadosAction() {
        $this->view->titulo = "Relatório de Certificados Emitidos";
    }
    
    public function relatorioCertificadosAction() {
        //desabilita layout
        $this->getHelper('layout')->disableLayout();
        
        $params = $this->_helper->util->utf8Decode($this->getAllParams());
             
        
        $alunos = $this->alunoDbTable->getDataGrid($params);
       
        foreach ($alunos as $indice => $aluno) {
            $certificadosEmitidos = $this->certificadoEmitidoDbTable->getCertificadosEmitidosToRelCertificados(array('id_aluno'=>$aluno['id_aluno']));            
            $alunos[$indice]['certificados_emitidos'] = $certificadosEmitidos;
        }
        
        $this->view->alunos = $alunos;
//        echo "<pre>";
//        print_r($this->view->alunos);
//        die();
    }
}

?>
