<?php
/*
 * 
 * @author Cesar Augusto Vieira Giovani
 * @date 25/08/2013
 * 
 */
class Aluno_CertificadoController extends Zend_Controller_Action {
    
    public function init() {
        $this->flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->msg = $this->flashMessenger->getMessages();
        
        $this->adpter = Zend_Db_Table_Abstract::getDefaultAdapter();        
                        
        $this->certificadoDbTable = new Application_Model_DbTable_Certificado();
        $this->certificadoEmitidoDbTable = new Application_Model_DbTable_CertificadoEmitido();
        
        $this->matriculaDbTable = new Application_Model_DbTable_Matricula();
        
        $this->alunoDbTable = new Application_Model_DbTable_Aluno();
        
        $this->view->menu = 'certificado';
    }                                       
    
    public function pesquisarAction() {
        $this->view->titulo = "Listagem de Certificados Emitidos";
    }
    
    public function gridPesquisarAction()
    {
        $this->getHelper('layout')->disableLayout();
        
        $params = $this->_helper->util->utf8Decode($this->getAllParams());
        
        $params['data_inicial'] = $this->_helper->util->urldecodeGet($this->_getParam('data_inicial'));
        $params['data_inicial'] = trim($this->_helper->util->reverseDate($params['data_inicial']));

        $params['data_final'] = $this->_helper->util->urldecodeGet($this->_getParam('data_final'));
        $params['data_final'] = trim($this->_helper->util->reverseDate($params['data_final']));                                       
        
        //Deixa fixo o id_aluno por ser o módulo de acesso utilizado pelos alunos
        $auth = Zend_Auth::getInstance(); 
        $usuario = Zend_Auth::getInstance()->getIdentity();
        $id_usuario = $usuario->id_usuario;
        $aluno = $this->alunoDbTable->fetchRow("id_usuario = '{$id_usuario}'")->toArray();
        $params['id_aluno'] = $aluno['id_aluno'];
        
        $certificadosEmitidos = $this->certificadoEmitidoDbTable->getDataGridPesquisar($params);                                                        
                                        
        $paginator = Zend_Paginator::factory($this->_helper->util->utf8Encode($certificadosEmitidos));
                
        
        
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setDefaultItemCountPerPage(5);
        $this->view->paginator = $paginator;                
    }
    
    public function imprimirCertificadoAction() {
        //desabilita layout
        $this->getHelper('layout')->disableLayout();
        
        //Caputara os parametros que chegarão pela url
        $params = $this->_getAllParams(); 
        
        $dadosCertificado = $this->certificadoEmitidoDbTable->getDataToImpressaoCertificado($params); 
        
        //Substitui as tags que foram escolhidas pelo usuário pelo seus respectivos valores
        $corpoCertificado = trim($dadosCertificado['tx_corpo']);
        $corpoCertificado = str_replace("[%nome_aluno%]", $dadosCertificado['tx_nome_aluno'], $corpoCertificado);
        $corpoCertificado = str_replace("[%cpf_aluno%]", $dadosCertificado['tx_cpf'], $corpoCertificado);
        $corpoCertificado = str_replace("[%nome_treinamento%]", $dadosCertificado['tx_nome_treinamento'], $corpoCertificado);
        $corpoCertificado = str_replace("[%nome_instrutor%]", $dadosCertificado['tx_nome_instrutor'], $corpoCertificado);
        $corpoCertificado = str_replace("[%funcao_instrutor%]", $dadosCertificado['tx_funcao_instrutor'], $corpoCertificado);
        $corpoCertificado = str_replace("[%dt_inicio_treinamento%]", $this->_helper->util->reverseDate($dadosCertificado['dt_inicio_treinamento']), $corpoCertificado);
        $corpoCertificado = str_replace("[%dt_termino_treinamento%]", $this->_helper->util->reverseDate($dadosCertificado['dt_termino_treinamento']), $corpoCertificado);
        $corpoCertificado = str_replace("[%inserir_carga_horaria%]", $this->_helper->util->reverseDate($dadosCertificado['nr_carga_horaria']), $corpoCertificado);
        $corpoCertificado = str_replace("[%nome_responsavel%]", $this->_helper->util->reverseDate($dadosCertificado['tx_nome_responsavel_tecnico']), $corpoCertificado);
        $corpoCertificado = str_replace("[%funcao_responsavel%]", $this->_helper->util->reverseDate($dadosCertificado['tx_funcao_responsavel_tecnico']), $corpoCertificado);                                
        $dadosCertificado['tx_corpo'] = $corpoCertificado;
        
        $this->view->dadosCertificado = $dadosCertificado;
        
        //print_r($dadosCertificado);
        //die();
    }
}

?>
