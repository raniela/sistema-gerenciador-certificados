<?php
/*
 * 
 * @author Cesar Augusto Vieira Giovani
 * @date 25/08/2013
 * 
 */
class Cliente_CertificadoController extends Zend_Controller_Action {
    
    public function init() {
        $this->flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->msg = $this->flashMessenger->getMessages();
        
        $this->adpter = Zend_Db_Table_Abstract::getDefaultAdapter();        
        $this->clienteDbTable = new Application_Model_DbTable_Cliente();
        
        $this->certificadoDbTable = new Application_Model_DbTable_Certificado();
        $this->certificadoEmitidoDbTable = new Application_Model_DbTable_CertificadoEmitido();
        
        $this->matriculaDbTable = new Application_Model_DbTable_Matricula();
        
        $this->view->menu = 'certificado';
    }
  
    public function gridAction() {
        $this->getHelper('layout')->disableLayout();
        
        $params['tx_nome_modelo'] = $this->_helper->util->utf8Decode($this->_helper->util->urldecodeGet($this->_getParam('tx_nome_modelo')));
        $params['tx_titulo'] = $this->_helper->util->utf8Decode($this->_helper->util->urldecodeGet($this->_getParam('tx_titulo')));        
                
        $certificados = $this->certificadoDbTable->getDataGrid($params);
                        
        $certificados = $this->_helper->util->utf8Encode($certificados);        
        //print_r($alunos);die();
        $paginator = Zend_Paginator::factory($certificados);
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setDefaultItemCountPerPage(10);
        $this->view->paginator = $paginator;
    }

    //Utilizada para buscar os certificados em tela modal 
    public function pesquisarCertificadoAction() {
        try {
            $this->getHelper('layout')->disableLayout();
            
            $params = $this->_getAllParams();
            $params = $this->_helper->util->urldecodeGet($params);            
            $params = $this->_helper->util->utf8Decode($params);                                                                        
            $params['limit'] = 5;                        
                                    
            $this->view->dataGrid = $this->_helper->util->utf8Encode($this->certificadoDbTable->getDataGrid($params)); 
                        
        } catch (Exception $e) {  
            echo $e->getMessage();
            die('ERRO|Ocorreu um erro ao tentar executar a operação. Tente novamente. Caso persista, contate o administrador do sistema.');
        }
    }
    
    public function gerarCertificadosAlunosAction() {
        $this->view->titulo = "Emitir Certificados dos Alunos Matriculados";
    }
    
    
    public function salvarCertificadosGeradosAction() {
        $this->getHelper('viewRenderer')->setNoRender();
        $this->getHelper('layout')->disableLayout();
        
        $matricula = $this->_helper->util->utf8Decode($this->getRequest()->getPost('matricula')); 
        $certificados = $this->_helper->util->utf8Decode($this->getRequest()->getPost('certificados'));                                             
        
        $this->adpter->beginTransaction();
        try {                             
            $id_turma = $matricula['id_turma'];
            $id_certificado = $matricula['id_certificado'];            
            
            $certificadoEmitido['id_certificado'] = $id_certificado;
            $certificadoEmitido['dt_emissao_certificado'] = date('Y-m-d');            
            foreach($certificados as $id_matricula => $nr_registro_certificado) {
                $certificadoEmitido['id_matricula'] = $id_matricula;
                $certificadoEmitido['nr_registro_certificado'] = $nr_registro_certificado;
                
                $this->certificadoEmitidoDbTable->delete("id_certificado = $id_certificado AND id_matricula = $id_matricula");
                
                $this->certificadoEmitidoDbTable->insert($certificadoEmitido);
            }                                 
            
            /** commita */
            $this->adpter->commit();                       
                        
            $this->_helper->json->sendJson(array(
                'tipo' => 'sucesso',
                'msg' => 'Certificados gerados com sucesso!',
                'url' => '/admin/certificado/index/'
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
        
        $logado = Zend_Auth::getInstance()->getIdentity();
        $id_usuario = $logado->id_usuario;
        $cliente_row = $this->clienteDbTable->fetchRow("id_usuario = '{$id_usuario}'")->toArray();
        
        $params['id_cliente'] = $cliente_row['id_cliente'];
       
        $certificadosEmitidos = $this->certificadoEmitidoDbTable->getDataGridPesquisarCertificado($params);                                                        
                                        
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
