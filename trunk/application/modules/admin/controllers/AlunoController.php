<?php
/*
 * 
 * @author Cesar Augusto Vieira Giovani
 * @date 25/08/2013
 * 
 */
class Admin_AlunoController extends Zend_Controller_Action {
    
    public function init() {
        $this->flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->msg = $this->flashMessenger->getMessages();
        
        $this->adpter = Zend_Db_Table_Abstract::getDefaultAdapter();        
                
        $this->clienteDbTable = new Application_Model_DbTable_Cliente();        
        $this->usuarioDbTable = new Application_Model_DbTable_Usuario();                
        $this->alunoDbTable = new Application_Model_DbTable_Aluno();
        
        $this->view->menu = 'aluno';
    }


    public function indexAction() {
        $this->view->titulo = "Listagem de Alunos";
    }
    
    public function gridAction() {
        $this->getHelper('layout')->disableLayout();
        
        $params['tx_nome_aluno'] = $this->_helper->util->utf8Decode($this->_helper->util->urldecodeGet($this->_getParam('tx_nome_aluno')));
        $params['tx_cpf'] = $this->_helper->util->urldecodeGet($this->_getParam('tx_cpf'));
                
        $alunos = $this->alunoDbTable->getDataGrid($params);
                        
        $alunos = $this->_helper->util->utf8Encode($alunos);        
        //print_r($alunos);die();
        $paginator = Zend_Paginator::factory($alunos);
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setDefaultItemCountPerPage(10);
        $this->view->paginator = $paginator;
    }

    public function formAction() {        
        //se já tem id é edição, tem que mandar os dados desse id pra view
        
        
        if ($this->getRequest()->getParam('id')) {
            /**
             * Edição do registro
             */
            $this->view->titulo = "Edição de Aluno";
            $id = $this->getRequest()->getParam('id');
                                    
            //busca os dados do aluno
            $aluno = $this->alunoDbTable->fetchRow("id_aluno = '{$id}'")->toArray();
            $id_cliente = $aluno['id_cliente'];
            $cliente = $this->clienteDbTable->fetchRow("id_cliente = '{$id_cliente}'")->toArray();            
            
            //Envia dados do cliente para a View
            $this->view->aluno = $this->_helper->util->utf8Encode($aluno);
            $this->view->cliente = $this->_helper->util->utf8Encode($cliente);            
        } else {
            /**
             * Cadastro do registro
             */
            //se for cadastro é só enviar o titulo
            $this->view->titulo = "Cadastro de Aluno";
        }                        
    }
    
    public function salvarAction() {
        $this->getHelper('viewRenderer')->setNoRender();
        $this->getHelper('layout')->disableLayout();
        
        $aluno = $this->_helper->util->utf8Decode($this->getRequest()->getPost('aluno')); 
                
        $salva = true;
        
        if(empty($aluno['id_aluno'])) {
            $alunoExistente = $this->alunoDbTable->fetchRow("tx_cpf = '{$aluno['tx_cpf']}'");
        } else {
            $alunoExistente = $this->alunoDbTable->fetchRow("tx_cpf = '{$aluno['tx_cpf']}' AND id_aluno <> '{$aluno['id_aluno']}'");
        }
        
        if(!empty($alunoExistente)) {
            $salva = false;
        }
        
        if(!$salva) {            
            $this->_helper->json->sendJson(array(
                'tipo' => 'erro',
                'msg' => 'Aluno já existente com esse CPF, verifique!',
                //'url' => '/admin/usuario/index/'
            ));
        } else {
            $this->adpter->beginTransaction();
            
            try {                                
                if (!empty($aluno['id_aluno'])) {
                    $id_aluno = $aluno['id_aluno'];                                        
                    $this->alunoDbTable->update($aluno, "id_aluno = {$id_aluno}");                                       
                } else {
                    $usuario['tx_nome'] = $aluno['tx_nome_aluno'];
                    $login = str_replace(".", "", $aluno['tx_cpf']);
                    $login = str_replace("-", "", $login); 
                    $usuario['tx_login'] = "ALUNO_".$login;                                        
                    $usuario['tx_senha'] = '12345';                                       
                    $usuario['tipo'] = 3;
                    $id_usuario = $this->usuarioDbTable->insert($usuario);
                    
                    $aluno['id_usuario'] = $id_usuario;
                    
                    $this->alunoDbTable->insert($aluno);                                                                                
                }            

                /** commita */
                $this->adpter->commit();

                $this->flashMessenger->addMessage('Salvo com sucesso!');

                $this->_helper->json->sendJson(array(
                    'tipo' => 'sucesso',
                    'msg' => 'Salvo com sucesso!',
                    'url' => '/admin/aluno/index/'
                ));
            } catch (Exception $exc) {
                /** executa rollback */
                $this->adpter->rollBack();
                
                $this->_helper->json->sendJson(array(
                    'tipo' => 'erro',
                    'msg' => $exc->getMessage(),
                    //'url' => '/admin/usuario/index/'
                ));                                        
            }
        }      
    }
        
    public function excluirAction() {
        $this->getHelper('viewRenderer')->setNoRender();
        $this->getHelper('layout')->disableLayout();
        
        /* inicia a transação */
        $this->adpter->beginTransaction();
        try {
            $id = $this->getRequest()->getParam('id');
            
            $aluno = $this->alunoDbTable->fetchRow("id_aluno = '{$id}'")->toArray();
            $id_usuario = $aluno['id_usuario'];
            
            $this->alunoDbTable->delete("id_aluno = $id");
            $this->usuarioDbTable->delete("id_usuario = $id_usuario");
                                    
            /** commita */
            $this->adpter->commit();
            
            $this->_helper->json->sendJson(array(
                    'tipo' => 'sucesso',
                    'msg' => 'Registro excluído com sucesso!',
                    'url' => '/admin/cliente/index/'
            ));                        
           
        } catch (Exception $exc) {
            $this->adpter->rollBack();
            //mensagem que retorna no json para javascript que no caso é de erro 
            if($exc->getCode() == 23000) {                
                $this->_helper->json->sendJson(array(
                    'tipo' => 'erro',
                    'msg' => 'Esse registro possui vínculos e não pode ser excluído, verifique!'                    
                ));
            } else {                
                $this->_helper->json->sendJson(array(
                    'tipo' => 'erro',
                    'msg' => $exc->getMessage() 
                ));
            }            
        }
    }
    
    public function importarAction() {                
        //se for cadastro é só enviar o titulo
        $this->view->titulo = "Importar Alunos";
                               
    }
    
    public function uploadAlunoAction()
    {
        //desabilita layout
        $this->getHelper('layout')->disableLayout();        
        
        
        /** Cria o diretorio do arquivo caso não exista*/
        $diretorioFiles = realpath(APPLICATION_PATH . "/../public/files") . '/';                        
        $diretorioFiles .= 'IMPORTAR';
        if(!is_dir($diretorioFiles)) {            
            mkdir($diretorioFiles, 0777, true);
        }
        
        
        
                                
        $extensoesPermitidas = array('xls','xlsx');
        
        $fileUploadAlunos = $_FILES['fileUploadAlunos'];
        if(!empty($fileUploadAlunos['name'])) {
            $extensao = explode(".", $fileUploadAlunos['name']);
            $extensao = $extensao[1];
        } else {
            $extensao = '';
        }
        
        
        
        
        if (in_array($extensao, $extensoesPermitidas) == false) {            
            $this->_helper->json->sendJson(array(
                    'tipo' => 'erro',
                    'msg' => 'O arquivo deve ser do tipo XLS/XLSX!',                    
            ));            
        }
        
        if($fileUploadAlunos['size'] > 10485760) {
            $this->_helper->json->sendJson(array(
                    'tipo' => 'erro',
                    'msg' => 'O arquivo não deve exceder 10 MB!',                    
            ));            
        }
                
        
        $urlArquivo = $this->_helper->upload->file('fileUploadAlunos', null, $extensoesPermitidas, $diretorioFiles.'/');        
        $diretorio = $diretorioFiles.'/'.$urlArquivo;
        
        if(!empty($urlArquivo)) {
            $this->_helper->json->sendJson(array(
                    'tipo' => 'sucesso',
                    'msg' => 'Planilha transferida com sucesso!',
                    'urlArquivo' => $urlArquivo
            )); 
        } else {
            $this->_helper->json->sendJson(array(
                    'tipo' => 'sucesso',
                    'msg' => 'Algum erro ocorreu durante a transferência da planilha, contate o administrador do sistema!',                    
            )); 
        }
                       
    }
    
}

?>
