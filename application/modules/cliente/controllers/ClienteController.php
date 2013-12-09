<?php

class Cliente_ClienteController extends Zend_Controller_Action {

    public function init() {
        $this->adpter = Zend_Db_Table_Abstract::getDefaultAdapter();

        $this->estadoDbTable = new Application_Model_DbTable_Estado();
        $this->clienteDbTable = new Application_Model_DbTable_Cliente();
        $this->usuarioDbTable = new Application_Model_DbTable_Usuario();
        $this->enderecoDbTable = new Application_Model_DbTable_Endereco();
        $this->alunoDbTable = new Application_Model_DbTable_Aluno();

        $this->view->menu = 'cliente';
    }

    public function indexAction() {
        $this->view->titulo = "Meus dados cadastrais";
    }

    public function formAction() {

        $logado = Zend_Auth::getInstance()->getIdentity();
        $id_usuario = $logado->id_usuario;

        $this->view->titulo = "Edição de dados cadastrais";

        $cliente_row = $this->clienteDbTable->fetchRow("id_usuario = '{$id_usuario}'")->toArray();
        $endereco_real = $this->enderecoDbTable->fetchRow("id_cliente = '{$cliente_row['id_cliente']}' AND tx_tipo_endereco='R'")->toArray();
        $endereco_correspondencia = $this->enderecoDbTable->fetchRow("id_cliente = '{$cliente_row['id_cliente']}' AND tx_tipo_endereco='C'")->toArray();

        //print_r($cliente_row); die;
        
        //Envia dados do cliente para a View
        $this->view->cliente = $this->_helper->util->utf8Encode($cliente_row);
        $this->view->endereco_real = $this->_helper->util->utf8Encode($endereco_real);
        $this->view->endereco_correspondencia = $this->_helper->util->utf8Encode($endereco_correspondencia);

        $dataComboEstados = $this->estadoDbTable->getDataCombo();
        $this->view->dataComboEstados = $this->_helper->util->utf8Encode($dataComboEstados);
    }

    public function salvarAction() {
        $this->getHelper('viewRenderer')->setNoRender();
        $this->getHelper('layout')->disableLayout();

        $cliente = $this->_helper->util->utf8Decode($this->getRequest()->getPost('cliente'));
        $endereco_real = $this->_helper->util->utf8Decode($this->getRequest()->getPost('endereco_real'));
        $endereco_correspondencia = $this->_helper->util->utf8Decode($this->getRequest()->getPost('endereco_correspondencia'));

        if ($cliente['tx_tipo_cliente'] == 'F') {
            $cliente['tx_nome_fantasia'] = null;
            $cliente['tx_razao_social'] = null;
            $cliente['tx_cnpj'] = null;
            $cliente['tx_inscricao_estadual'] = null;
            $cliente['tx_inscricao_municipal'] = null;
            $cliente['tx_nome_contato'] = null;
        } else {
            $cliente['tx_nome'] = null;
            $cliente['tx_cliente_aluno'] = null;
            $cliente['tx_rg'] = null;
            $cliente['tx_cpf'] = null;
            $cliente['tx_profissao'] = null;
        }

        $this->adpter->beginTransaction();
        try {

            if (!empty($cliente['id_cliente'])) {
                $id_cliente = $cliente['id_cliente'];
                $this->clienteDbTable->update($cliente, "id_cliente = {$id_cliente}");

                if (empty($endereco_real['id_estado'])) {
                    $endereco_real['id_estado'] = null;
                }

                $id_endereco = $endereco_real['id_endereco'];
                $this->enderecoDbTable->update($endereco_real, "id_endereco = {$id_endereco}");

                if (empty($endereco_correspondencia['id_estado'])) {
                    $endereco_correspondencia['id_estado'] = null;
                }

                $id_endereco = $endereco_correspondencia['id_endereco'];
                $this->enderecoDbTable->update($endereco_correspondencia, "id_endereco = {$id_endereco}");
            }

            /** commita */
            $this->adpter->commit();

            $this->_helper->json->sendJson(array(
                'tipo' => 'sucesso',
                'msg' => 'Salvo com sucesso!',
                'url' => '/cliente/'
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

    //Utilizada para buscar os clientes em tela modal 
    public function pesquisarClienteAction() {
        try {
            $this->getHelper('layout')->disableLayout();

            $params = $this->_getAllParams();
            $params = $this->_helper->util->urldecodeGet($params);
            $params = $this->_helper->util->utf8Decode($params);
            $params['limit'] = 5;

            if ($params['tx_tipo_cliente'] == 'F') {
                $params['tx_razao_social'] = null;
                $params['tx_cnpj'] = null;
            } else {
                $params['tx_nome'] = null;
                $params['tx_cpf'] = null;
            }

            $this->view->dataGrid = $this->_helper->util->utf8Encode($this->clienteDbTable->getDataGrid($params));

            //print_r($this->view->dataGrid);
            //die();
        } catch (Exception $e) {
            echo $e->getMessage();
            die('ERRO|Ocorreu um erro ao tentar executar a operação. Tente novamente. Caso persista, contate o administrador do sistema.');
        }
    }

}

?>
