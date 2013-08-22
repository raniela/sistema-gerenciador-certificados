<?php
/*
 * 
 * @author Cesar Augusto Vieira Giovani
 * @date 16/08/2013
 * 
 */
class Admin_ClienteController extends Zend_Controller_Action {
    
    public function init() {
        $this->flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->msg = $this->flashMessenger->getMessages();
        
        $this->adpter = Zend_Db_Table_Abstract::getDefaultAdapter();        
        
        $this->estadoDbTable = new Application_Model_DbTable_Estado();
        $this->clienteDbTable = new Application_Model_DbTable_Cliente();        
        $this->usuarioDbTable = new Application_Model_DbTable_Usuario();        
        $this->enderecoDbTable = new Application_Model_DbTable_Endereco();
        $this->alunoDbTable = new Application_Model_DbTable_Aluno();
    }


    public function indexAction() {
        $this->view->titulo = "Listagem de Clientes";
    }
    
    public function gridAction() {
        $this->getHelper('layout')->disableLayout();
        
        $params['tx_tipo_cliente'] = $this->_getParam('tx_tipo_cliente');
        if($params['tx_tipo_cliente'] == 'F') {
            $params['tx_nome'] = $this->_helper->util->utf8Decode($this->_helper->util->urldecodeGet($this->_getParam('tx_nome')));
            $params['tx_cpf'] = $this->_helper->util->urldecodeGet($this->_getParam('tx_cpf'));
        } else {
            $params['tx_razao_social'] = $this->_helper->util->utf8Decode($this->_helper->util->urldecodeGet($this->_getParam('tx_razao_social')));        
            $params['tx_cnpj'] = $this->_helper->util->urldecodeGet($this->_getParam('tx_cnpj'));
        }
        
        
        $clientes = $this->clienteDbTable->getDataGrid($params);
                        
        $clientes = $this->_helper->util->utf8Encode($clientes);        
        $paginator = Zend_Paginator::factory($clientes);
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setDefaultItemCountPerPage(10);
        $this->view->paginator = $paginator;
        
        //print_r($clientes);
        //die();
    }

    public function formAction() {        
        //se já tem id é edição, tem que mandar os dados desse id pra view
        
        
        if ($this->getRequest()->getParam('id')) {
            /**
             * Edição do registro
             */
            $this->view->titulo = "Edição de Cliente";
            $id = $this->getRequest()->getParam('id');
                                    
            //busca os dados do cliente
            $cliente = $this->clienteDbTable->fetchRow("id_cliente = '{$id}'")->toArray();
            $endereco_real = $this->enderecoDbTable->fetchRow("id_cliente = '{$id}' AND tx_tipo_endereco='R'")->toArray();
            $endereco_correspondencia = $this->enderecoDbTable->fetchRow("id_cliente = '{$id}' AND tx_tipo_endereco='C'")->toArray();                        
            
            //Envia dados do cliente para a View
            $this->view->cliente = $this->_helper->util->utf8Encode($cliente);
            $this->view->endereco_real = $this->_helper->util->utf8Encode($endereco_real);
            $this->view->endereco_correspondencia = $this->_helper->util->utf8Encode($endereco_correspondencia);
        } else {
            /**
             * Cadastro do registro
             */
            //se for cadastro é só enviar o titulo
            $this->view->titulo = "Cadastro de Cliente";
        }
        
        
        $dataComboEstados = $this->estadoDbTable->getDataCombo();
        $this->view->dataComboEstados = $this->_helper->util->utf8Encode($dataComboEstados);
    }
    
    public function salvarAction() {
        $this->getHelper('viewRenderer')->setNoRender();
        $this->getHelper('layout')->disableLayout();
        
        $cliente = $this->_helper->util->utf8Decode($this->getRequest()->getPost('cliente')); 
        $endereco_real = $this->_helper->util->utf8Decode($this->getRequest()->getPost('endereco_real')); 
        $endereco_correspondencia = $this->_helper->util->utf8Decode($this->getRequest()->getPost('endereco_correspondencia')); 
        
        $salva = true;
        
        if($cliente['tx_tipo_cliente'] == 'F') {
            $cliente['tx_nome_fantasia'] = null;
            $cliente['tx_razao_social'] = null;
            $cliente['tx_cnpj'] = null;
            $cliente['tx_inscricao_estadual'] = null;
            $cliente['tx_isncricao_municipal'] = null;
            $cliente['tx_nome_contato'] = null;  
            
            if(empty($cliente['id_cliente'])) {
                $clienteExistente = $this->clienteDbTable->fetchRow("tx_cpf = '{$cliente['tx_cpf']}'");
            } else {
                $clienteExistente = $this->clienteDbTable->fetchRow("tx_cpf = '{$cliente['tx_cpf']}' AND id_cliente <> '{$cliente['id_cliente']}'");
            }
        } else {
            $cliente['tx_nome'] = null;
            $cliente['tx_cliente_aluno'] = null;
            $cliente['tx_rg'] = null;
            $cliente['tx_cpf'] = null;
            $cliente['tx_profissao'] = null;
            
            if(empty($cliente['id_cliente'])) {
                $clienteExistente = $this->clienteDbTable->fetchRow("tx_cnpj = '{$cliente['tx_cnpj']}'");
            } else {
                $clienteExistente = $this->clienteDbTable->fetchRow("tx_cnpj = '{$cliente['tx_cnpj']}' AND id_cliente <> '{$cliente['id_cliente']}'");
            }
        }
        
        if(!empty($clienteExistente)) {
            $salva = false;
        }
        
        if(!$salva) {            
            $this->_helper->json->sendJson(array(
                'tipo' => 'erro',
                'msg' => 'Cliente já existente com esse CPF/CNPJ!',
                //'url' => '/admin/usuario/index/'
            ));
        } else {
            $this->adpter->beginTransaction();
            try {
                
                
                if (!empty($cliente['id_cliente'])) {
                    $id_cliente = $cliente['id_cliente'];                                        
                    $this->clienteDbTable->update($cliente, "id_cliente = {$id_cliente}");
                    
                    if(empty($endereco_real['id_estado'])) {
                        $endereco_real['id_estado'] = null;
                    }
                    
                    $id_endereco = $endereco_real['id_endereco'];
                    $this->enderecoDbTable->update($endereco_real, "id_endereco = {$id_endereco}");
                    
                    if(empty($endereco_correspondencia['id_estado'])) {
                        $endereco_correspondencia['id_estado'] = null;
                    }
                    
                    $id_endereco = $endereco_correspondencia['id_endereco'];
                    $this->enderecoDbTable->update($endereco_correspondencia, "id_endereco = {$id_endereco}");
                } else {
                    $usuario['tx_nome'] = empty($cliente['tx_nome']) ? $cliente['tx_razao_social'] : $cliente['tx_nome'];
                    
                    if($cliente['tx_tipo_cliente'] == 'F') {
                        $login = str_replace(".", "", $cliente['tx_cpf']);
                        $login = str_replace("-", "", $login);                                                
                    } else {
                        $login = str_replace(".", "", $cliente['tx_cnpj']);
                        $login = str_replace("-", "", $login);
                        $login = str_replace("/", "", $login);
                    }
                    $usuario['tx_login'] = "CLIENTE_".$login;
                    $usuario['tx_senha'] = '12345';
                    $usuario['tx_email'] = $cliente['tx_email'];                    
                    $usuario['tipo'] = 2;
                    $id_usuario = $this->usuarioDbTable->insert($usuario);
                    //die($id_usuario);                    
                    $cliente['id_usuario'] = $id_usuario;
                    $id_cliente = $this->clienteDbTable->insert($cliente);                                        
                    
                    $endereco_real['id_cliente'] = $id_cliente;
                    if(empty($endereco_real['id_estado'])) {
                        $endereco_real['id_estado'] = null;
                    }
                    $this->enderecoDbTable->insert($endereco_real);
                    
                    $endereco_correspondencia['id_cliente'] = $id_cliente;
                    if(empty($endereco_correspondencia['id_estado'])) {
                        $endereco_correspondencia['id_estado'] = null;
                    }
                    $this->enderecoDbTable->insert($endereco_correspondencia);
                    
                    if($cliente['tx_cliente_aluno'] == 'S') {
                        $usuario_aluno['tx_nome'] = $cliente['tx_nome'];
                        $usuario_aluno['tx_login'] = "ALUNO_".$login;
                        $usuario_aluno['tx_senha'] = '12345';
                        $usuario_aluno['tx_email'] = $cliente['tx_email'];                            
                        $usuario_aluno['tipo'] = 3;
                        $id_usuario_aluno = $this->usuarioDbTable->insert($usuario_aluno);
                        
                        $aluno['id_cliente'] = $id_cliente;
                        $aluno['id_usuario'] = $id_usuario_aluno;
                        $aluno['tx_nome_aluno'] = $cliente['tx_nome'];
                        $aluno['tx_rg'] = $cliente['tx_rg'];
                        $aluno['tx_cpf'] = $cliente['tx_cpf'];
                        $aluno['tx_cargo'] = $cliente['tx_profissao'];
                        
                        $this->alunoDbTable->insert($aluno);
                    }
                }            

                /** commita */
                $this->adpter->commit();

                $this->flashMessenger->addMessage('Salvo com sucesso!');

                $this->_helper->json->sendJson(array(
                    'tipo' => 'sucesso',
                    'msg' => 'Salvo com sucesso!',
                    'url' => '/admin/cliente/index/'
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
        
        
        //print_r($cliente); 
        //print_r($endereco_real); 
        //print_r($endereco_correspondencia); 
        //die();        
    }
    
    
    public function excluirAction() {
        $this->getHelper('viewRenderer')->setNoRender();
        $this->getHelper('layout')->disableLayout();
        
        /* inicia a transação */
        $this->adpter->beginTransaction();
        try {
            $id = $this->getRequest()->getParam('id');
            
            $cliente = $this->clienteDbTable->fetchRow("id_cliente = '{$id}'")->toArray();
            $id_usuario = $cliente['id_usuario'];
            
            $this->enderecoDbTable->delete("id_cliente = $id");
            $this->clienteDbTable->delete("id_cliente = $id");
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
    
}

?>
