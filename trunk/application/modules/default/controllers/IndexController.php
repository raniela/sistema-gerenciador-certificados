<?php

class IndexController extends Zend_Controller_Action {

    public function indexAction() {

        $this->view->titulo = "Sistema Gerenciador de Certificados";

        if ($this->getRequest()->isPost()) {

            $auth = Zend_Auth::getInstance();

            $db = Zend_Db_Table::getDefaultAdapter();

            $login = $this->getRequest()->getPost('tx_login');
            $senha = $this->getRequest()->getPost('tx_senha');

            if (empty($login)) {
                $login = "";
            }

            if (empty($senha)) {
                $senha = " ";
            }

            /* usa a classe Zend_Auth_Adapter_DbTable na autenticação */
            $authAdapterDbTable = new Zend_Auth_Adapter_DbTable($db);

            $authAdapterDbTable->setTableName('usuario') //nome da tabela usada para autenticar
                    ->setIdentityColumn('tx_login') // nome da coluna de login
                    ->setCredentialColumn('tx_senha') // nome da coluna de senha
                    ->setIdentity($login) // valor para testar o login
                    ->setCredential($senha); // valor para testar a senha
            
            /* autentica usuário */
            $result = $auth->authenticate($authAdapterDbTable); // tenta autenticar e retorna um codigo para testar
            //Zend_Debug::dump($result);
            //die;

            /* verifica o codigo e lança uma exceção */
            switch ($result->getCode()) { // testa o codigo
                case Zend_Auth_Result::SUCCESS:
                    
                    /* pega os dados do usuario */
                    $resultRow = $authAdapterDbTable->getResultRowObject();

                    /* salva o usuario na sessão */
                    $auth->getStorage()->write($resultRow);

                    /* Salva a data do login como ultimo acesso */
                    //$this->usuarioDbTable = new Application_Model_DbTable_Usuario();
                    //$usuario['dt_ultimo_acesso'] = date('Y-m-d H:i:s');
                    //$this->usuarioDbTable->update($usuario, "id_usuario = {$resultRow->id_usuario}");

                     /* Pega o usuario logado */
                    $usuario = Zend_Auth::getInstance()->getIdentity();
                    
                    //print_r($usuario); die;
                    
                    $tipo_usuario = $usuario->tx_tipo_usuario;
                    
                    //$ident = $auth->getIdentity();
                      //Zend_Debug::dump($ident);
                      //die;
                    /* Direciona o usuário para o modulo de acordo com o tipo */
                    if($tipo_usuario == USUARIO_ADMIN){
                        $this->_redirect('/admin/index');                        
                    }elseif ($tipo_usuario == USUARIO_ALUNO) {
                        $this->redirect('/aluno/index');
                    }elseif ($tipo_usuario == USUARIO_CLIENTE) {
                        $this->redirect('/cliente/index');
                    }
                   
                    //$this->view->usuario = $usuario;
                     
                    break;
                case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
                    die("Nome de usuario não existe");
                    break;
                case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
                    die("Senha invalida");
                    break;
                default :
                    die("Um erro desconhecido ocorreu, entre em contato com a administração");
                    break;
            }
        }
    }

    public function sairAction() {
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
        $this->_redirect('/default/index');
    }

}

