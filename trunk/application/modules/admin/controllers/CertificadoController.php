<?php
/*
 * 
 * @author Cesar Augusto Vieira Giovani
 * @date 25/08/2013
 * 
 */
class Admin_CertificadoController extends Zend_Controller_Action {
    
    public function init() {
        $this->flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->msg = $this->flashMessenger->getMessages();
        
        $this->adpter = Zend_Db_Table_Abstract::getDefaultAdapter();        
                        
        $this->certificadoDbTable = new Application_Model_DbTable_Certificado();
        $this->certificadoEmitidoDbTable = new Application_Model_DbTable_CertificadoEmitido();
        
        $this->matriculaDbTable = new Application_Model_DbTable_Matricula();
        
        $this->view->menu = 'certificado';
    }


    public function indexAction() {
        $this->view->titulo = "Listagem de Modelos de Certificados";
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

    public function formAction() {        
        //se já tem id é edição, tem que mandar os dados desse id pra view                
        if ($this->getRequest()->getParam('id')) {
            /**
             * Edição do registro
             */
            $this->view->titulo = "Edição de Modelo de Certificado";
            $id = $this->getRequest()->getParam('id');
                                    
            //busca os dados do modelo de certificado
            $certificado = $this->certificadoDbTable->fetchRow("id_certificado = '{$id}'")->toArray();
                        
            if(!empty($certificado['tx_url_logotipo_cabecalho_esquerda'])) {
                $this->view->topo_esquerdo = $this->_getHTMLfotoAlunoComPath($certificado['tx_url_logotipo_cabecalho_esquerda'],'cabecalho','topo_esquerdo');
            }
            
            if(!empty($certificado['tx_url_logotipo_cabecalho_centro'])) {
                $this->view->topo_centro = $this->_getHTMLfotoAlunoComPath($certificado['tx_url_logotipo_cabecalho_centro'],'cabecalho','topo_centro');
            }
            
            if(!empty($certificado['tx_url_logotipo_cabecalho_direita'])) {
                $this->view->topo_direito = $this->_getHTMLfotoAlunoComPath($certificado['tx_url_logotipo_cabecalho_direita'],'cabecalho','topo_direito');
            }
                        
            
            if(!empty($certificado['tx_url_logotipo_rodape_esquerda'])) {
                $this->view->rodape_esquerdo = $this->_getHTMLfotoAlunoComPath($certificado['tx_url_logotipo_rodape_esquerda'],'rodape','rodape_esquerdo');
            }
            
            if(!empty($certificado['tx_url_logotipo_rodape_direita'])) {
                $this->view->rodape_direito = $this->_getHTMLfotoAlunoComPath($certificado['tx_url_logotipo_rodape_direita'],'rodape','rodape_direito');
            }
            
            //Envia dados do cliente para a View
            $this->view->certificado = $this->_helper->util->utf8Encode($certificado);            
        } else {
            /**
             * Cadastro do registro
             */
            //se for cadastro é só enviar o titulo
            $this->view->titulo = "Cadastro de Modelo de Certificado";
        }                        
    }
    
    public function salvarAction() {
        $this->getHelper('viewRenderer')->setNoRender();
        $this->getHelper('layout')->disableLayout();
        
        $certificado = $this->_helper->util->utf8Decode($this->getRequest()->getPost('certificado'));                
        
        $salva = true;
        
        if(empty($certificado['id_certificado'])) {
            $certificadoExistente = $this->certificadoDbTable->fetchRow("tx_nome_modelo = '{$certificado['tx_nome_modelo']}'");
        } else {
            $certificadoExistente = $this->certificadoDbTable->fetchRow("tx_nome_modelo = '{$certificado['tx_nome_modelo']}' AND id_certificado <> '{$certificado['id_certificado']}'");
        }
        
        if(!empty($certificadoExistente)) {
            $salva = false;
        }
        
        if(!$salva) {            
            $this->_helper->json->sendJson(array(
                'tipo' => 'erro',
                'msg' => 'Modelo já existente com esse Nome, verifique!',
                //'url' => '/admin/usuario/index/'
            ));
        } else {
            $this->adpter->beginTransaction();
            
            try {                                
                if (!empty($certificado['id_certificado'])) {
                    $id_certificado = $certificado['id_certificado'];       
                    
                    $certificadoAntigo = $this->certificadoDbTable->fetchRow("id_certificado = '{$id_certificado}'")->toArray();
                    
                    $diretorioImgCab = realpath(APPLICATION_PATH . "/../public/img/logos_certificado/cabecalho");
                    $diretorioImgRod = realpath(APPLICATION_PATH . "/../public/img/logos_certificado/rodape");           

                    if(!empty($certificadoAntigo['tx_url_logotipo_cabecalho_esquerda']) && ($certificadoAntigo['tx_url_logotipo_cabecalho_esquerda'] != $certificado['tx_url_logotipo_cabecalho_esquerda'])) {
                        $arrayLogo = explode(".", $certificadoAntigo['tx_url_logotipo_cabecalho_esquerda']);

                        $caminhoPlanilha = $diretorioImgCab . "\\" . "{$arrayLogo[0]}*.*";                
                        array_map('unlink', glob($caminhoPlanilha)); 
                    }

                    if(!empty($certificadoAntigo['tx_url_logotipo_cabecalho_centro']) && ($certificadoAntigo['tx_url_logotipo_cabecalho_centro'] != $certificado['tx_url_logotipo_cabecalho_centro'])) {
                        $arrayLogo = explode(".", $certificadoAntigo['tx_url_logotipo_cabecalho_centro']);

                        $caminhoPlanilha = $diretorioImgCab . "/" . "{$arrayLogo[0]}*.*";
                        array_map('unlink', glob($caminhoPlanilha)); 
                    }

                    if(!empty($certificadoAntigo['tx_url_logotipo_cabecalho_direita']) && ($certificadoAntigo['tx_url_logotipo_cabecalho_direita'] != $certificado['tx_url_logotipo_cabecalho_direita'])) {
                        $arrayLogo = explode(".", $certificadoAntigo['tx_url_logotipo_cabecalho_direita']);

                        $caminhoPlanilha = $diretorioImgCab . "/" . "{$arrayLogo[0]}*.*";
                        array_map('unlink', glob($caminhoPlanilha)); 
                    }

                    if(!empty($certificadoAntigo['tx_url_logotipo_rodape_esquerda']) && ($certificadoAntigo['tx_url_logotipo_rodape_esquerda'] != $certificado['tx_url_logotipo_rodape_esquerda'])) {
                        $arrayLogo = explode(".", $certificadoAntigo['tx_url_logotipo_rodape_esquerda']);

                        $caminhoPlanilha = $diretorioImgRod . "/" . "{$arrayLogo[0]}*.*";
                        array_map('unlink', glob($caminhoPlanilha)); 
                    }

                    if(!empty($certificadoAntigo['tx_url_logotipo_rodape_direita']) && ($certificadoAntigo['tx_url_logotipo_rodape_direita'] != $certificado['tx_url_logotipo_rodape_direita'])) {
                        $arrayLogo = explode(".", $certificadoAntigo['tx_url_logotipo_rodape_direita']);

                        $caminhoPlanilha = $diretorioImgRod . "/" . "{$arrayLogo[0]}*.*";
                        array_map('unlink', glob($caminhoPlanilha)); 
                    }
                    
                    $this->certificadoDbTable->update($certificado, "id_certificado = {$id_certificado}");                                       
                } else {                                        
                    $this->certificadoDbTable->insert($certificado);                                                                                
                }            

                /** commita */
                $this->adpter->commit();                

                $this->_helper->json->sendJson(array(
                    'tipo' => 'sucesso',
                    'msg' => 'Salvo com sucesso!',
                    'url' => '/admin/certificado/index/'
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
            
            $certificado = $this->certificadoDbTable->fetchRow("id_certificado = '{$id}'")->toArray();                                    
            
            $this->certificadoDbTable->delete("id_certificado = $id");                        
                                    
            /** commita */
            $this->adpter->commit();
            
            $diretorioImgCab = realpath(APPLICATION_PATH . "/../public/img/logos_certificado/cabecalho");
            $diretorioImgRod = realpath(APPLICATION_PATH . "/../public/img/logos_certificado/rodape");           
            
            if(!empty($certificado['tx_url_logotipo_cabecalho_esquerda'])) {
                $arrayLogo = explode(".", $certificado['tx_url_logotipo_cabecalho_esquerda']);

                $caminhoPlanilha = $diretorioImgCab . "\\" . "{$arrayLogo[0]}*.*";                
                array_map('unlink', glob($caminhoPlanilha)); 
            }
            
            if(!empty($certificado['tx_url_logotipo_cabecalho_centro'])) {
                $arrayLogo = explode(".", $certificado['tx_url_logotipo_cabecalho_centro']);

                $caminhoPlanilha = $diretorioImgCab . "/" . "{$arrayLogo[0]}*.*";
                array_map('unlink', glob($caminhoPlanilha)); 
            }
            
            if(!empty($certificado['tx_url_logotipo_cabecalho_direita'])) {
                $arrayLogo = explode(".", $certificado['tx_url_logotipo_cabecalho_direita']);

                $caminhoPlanilha = $diretorioImgCab . "/" . "{$arrayLogo[0]}*.*";
                array_map('unlink', glob($caminhoPlanilha)); 
            }
            
            if(!empty($certificado['tx_url_logotipo_rodape_esquerda'])) {
                $arrayLogo = explode(".", $certificado['tx_url_logotipo_rodape_esquerda']);

                $caminhoPlanilha = $diretorioImgRod . "/" . "{$arrayLogo[0]}*.*";
                array_map('unlink', glob($caminhoPlanilha)); 
            }
            
            if(!empty($certificado['tx_url_logotipo_rodape_direita'])) {
                $arrayLogo = explode(".", $certificado['tx_url_logotipo_rodape_direita']);

                $caminhoPlanilha = $diretorioImgRod . "/" . "{$arrayLogo[0]}*.*";
                array_map('unlink', glob($caminhoPlanilha)); 
            }
            
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
    
    public function uploadLogoTopoEsquerdoAction()
    {
        //desabilita layout
        $this->getHelper('layout')->disableLayout();        
        
        
        /** Cria o diretorio do arquivo caso não exista*/
        $diretorioFiles = realpath(APPLICATION_PATH . "/../public/img/logos_certificado") . '/';                        
        $diretorioFiles .= 'cabecalho';
        if(!is_dir($diretorioFiles)) {            
            mkdir($diretorioFiles, 0777, true);
        }
                                                        
        $extensoesPermitidas = array('swf', 'gif', 'jpeg', 'jpg', 'png');
        
        $fileUploadLogoTopoEsquerda = $_FILES['fileUploadLogoTopoEsquerda'];
        if(!empty($fileUploadLogoTopoEsquerda['name'])) {
            $extensao = explode(".", $fileUploadLogoTopoEsquerda['name']);
            $extensao = $extensao[1];
        } else {
            $extensao = '';
        }                        
        
        if (in_array($extensao, $extensoesPermitidas) == false) {            
            $this->_helper->json->sendJson(array(
                    'tipo' => 'erro',
                    'msg' => 'O arquivo deve ser do tipo SWF/GIF/JPEG/JPG/PNG!',                    
            ));            
        }
        
        if($fileUploadLogoTopoEsquerda['size'] > 10485760) {
            $this->_helper->json->sendJson(array(
                    'tipo' => 'erro',
                    'msg' => 'O arquivo não deve exceder 10 MB!',                    
            ));            
        }
         
        $urlArquivo = $this->_helper->upload->file('fileUploadLogoTopoEsquerda', null, $extensoesPermitidas, $diretorioFiles.'/');        
        $diretorio = $diretorioFiles.'/'.$urlArquivo;
        
        $htmlImagem = $this->_getHTMLfotoAlunoComPath($urlArquivo,'cabecalho','topo_esquerdo');
        
        if(!empty($urlArquivo)) {
            $this->_helper->json->sendJson(array(
                    'tipo' => 'sucesso',
                    'msg' => 'Imagem transferida com sucesso!',
                    'urlArquivo' => $urlArquivo,
                    'htmlImagem' => $htmlImagem
            )); 
        } else {
            $this->_helper->json->sendJson(array(
                    'tipo' => 'sucesso',
                    'msg' => 'Algum erro ocorreu durante a transferência da image, contate o administrador do sistema!',                    
            )); 
        }                       
    }
    
    public function uploadLogoTopoCentroAction()
    {
        //desabilita layout
        $this->getHelper('layout')->disableLayout();        
                
        /** Cria o diretorio do arquivo caso não exista*/
        $diretorioFiles = realpath(APPLICATION_PATH . "/../public/img/logos_certificado") . '/';                        
        $diretorioFiles .= 'cabecalho';
        if(!is_dir($diretorioFiles)) {            
            mkdir($diretorioFiles, 0777, true);
        }
                                                        
        $extensoesPermitidas = array('swf', 'gif', 'jpeg', 'jpg', 'png');
        
        $fileUploadLogoTopoEsquerda = $_FILES['fileUploadLogoTopoCentro'];
        if(!empty($fileUploadLogoTopoEsquerda['name'])) {
            $extensao = explode(".", $fileUploadLogoTopoEsquerda['name']);
            $extensao = $extensao[1];
        } else {
            $extensao = '';
        }                        
        
        if (in_array($extensao, $extensoesPermitidas) == false) {            
            $this->_helper->json->sendJson(array(
                    'tipo' => 'erro',
                    'msg' => 'O arquivo deve ser do tipo SWF/GIF/JPEG/JPG/PNG!',                    
            ));            
        }
        
        if($fileUploadLogoTopoEsquerda['size'] > 10485760) {
            $this->_helper->json->sendJson(array(
                    'tipo' => 'erro',
                    'msg' => 'O arquivo não deve exceder 10 MB!',                    
            ));            
        }
         
        $urlArquivo = $this->_helper->upload->file('fileUploadLogoTopoCentro', null, $extensoesPermitidas, $diretorioFiles.'/');        
        $diretorio = $diretorioFiles.'/'.$urlArquivo;
        
        $htmlImagem = $this->_getHTMLfotoAlunoComPath($urlArquivo,'cabecalho','topo_centro');
        
        if(!empty($urlArquivo)) {
            $this->_helper->json->sendJson(array(
                    'tipo' => 'sucesso',
                    'msg' => 'Imagem transferida com sucesso!',
                    'urlArquivo' => $urlArquivo,
                    'htmlImagem' => $htmlImagem
            )); 
        } else {
            $this->_helper->json->sendJson(array(
                    'tipo' => 'sucesso',
                    'msg' => 'Algum erro ocorreu durante a transferência da image, contate o administrador do sistema!',                    
            )); 
        }                       
    }
    
    public function uploadLogoTopoDireitaAction()
    {
        //desabilita layout
        $this->getHelper('layout')->disableLayout();        
        
        
        /** Cria o diretorio do arquivo caso não exista*/
        $diretorioFiles = realpath(APPLICATION_PATH . "/../public/img/logos_certificado") . '/';                        
        $diretorioFiles .= 'cabecalho';
        if(!is_dir($diretorioFiles)) {            
            mkdir($diretorioFiles, 0777, true);
        }
                                                        
        $extensoesPermitidas = array('swf', 'gif', 'jpeg', 'jpg', 'png');
        
        $fileUploadLogoTopoEsquerda = $_FILES['fileUploadLogoTopoDireita'];
        if(!empty($fileUploadLogoTopoEsquerda['name'])) {
            $extensao = explode(".", $fileUploadLogoTopoEsquerda['name']);
            $extensao = $extensao[1];
        } else {
            $extensao = '';
        }                        
        
        if (in_array($extensao, $extensoesPermitidas) == false) {            
            $this->_helper->json->sendJson(array(
                    'tipo' => 'erro',
                    'msg' => 'O arquivo deve ser do tipo SWF/GIF/JPEG/JPG/PNG!',                    
            ));            
        }
        
        if($fileUploadLogoTopoEsquerda['size'] > 10485760) {
            $this->_helper->json->sendJson(array(
                    'tipo' => 'erro',
                    'msg' => 'O arquivo não deve exceder 10 MB!',                    
            ));            
        }
         
        $urlArquivo = $this->_helper->upload->file('fileUploadLogoTopoDireita', null, $extensoesPermitidas, $diretorioFiles.'/');        
        $diretorio = $diretorioFiles.'/'.$urlArquivo;
        
        $htmlImagem = $this->_getHTMLfotoAlunoComPath($urlArquivo,'cabecalho','topo_direito');
        
        if(!empty($urlArquivo)) {
            $this->_helper->json->sendJson(array(
                    'tipo' => 'sucesso',
                    'msg' => 'Imagem transferida com sucesso!',
                    'urlArquivo' => $urlArquivo,
                    'htmlImagem' => $htmlImagem
            )); 
        } else {
            $this->_helper->json->sendJson(array(
                    'tipo' => 'sucesso',
                    'msg' => 'Algum erro ocorreu durante a transferência da image, contate o administrador do sistema!',                    
            )); 
        }                       
    }
    
    public function uploadLogoRodapeEsquerdoAction()
    {
        //desabilita layout
        $this->getHelper('layout')->disableLayout();        
        
        
        /** Cria o diretorio do arquivo caso não exista*/
        $diretorioFiles = realpath(APPLICATION_PATH . "/../public/img/logos_certificado") . '/';                        
        $diretorioFiles .= 'rodape';
        if(!is_dir($diretorioFiles)) {            
            mkdir($diretorioFiles, 0777, true);
        }
                                                        
        $extensoesPermitidas = array('swf', 'gif', 'jpeg', 'jpg', 'png');
        
        $fileUploadLogoTopoEsquerda = $_FILES['fileUploadLogoRodapeEsquerdo'];
        if(!empty($fileUploadLogoTopoEsquerda['name'])) {
            $extensao = explode(".", $fileUploadLogoTopoEsquerda['name']);
            $extensao = $extensao[1];
        } else {
            $extensao = '';
        }                        
        
        if (in_array($extensao, $extensoesPermitidas) == false) {            
            $this->_helper->json->sendJson(array(
                    'tipo' => 'erro',
                    'msg' => 'O arquivo deve ser do tipo SWF/GIF/JPEG/JPG/PNG!',                    
            ));            
        }
        
        if($fileUploadLogoTopoEsquerda['size'] > 10485760) {
            $this->_helper->json->sendJson(array(
                    'tipo' => 'erro',
                    'msg' => 'O arquivo não deve exceder 10 MB!',                    
            ));            
        }
         
        $urlArquivo = $this->_helper->upload->file('fileUploadLogoRodapeEsquerdo', null, $extensoesPermitidas, $diretorioFiles.'/');        
        $diretorio = $diretorioFiles.'/'.$urlArquivo;
        
        $htmlImagem = $this->_getHTMLfotoAlunoComPath($urlArquivo, 'rodape','rodape_esquerdo');
        
        if(!empty($urlArquivo)) {
            $this->_helper->json->sendJson(array(
                    'tipo' => 'sucesso',
                    'msg' => 'Imagem transferida com sucesso!',
                    'urlArquivo' => $urlArquivo,
                    'htmlImagem' => $htmlImagem
            )); 
        } else {
            $this->_helper->json->sendJson(array(
                    'tipo' => 'sucesso',
                    'msg' => 'Algum erro ocorreu durante a transferência da image, contate o administrador do sistema!',                    
            )); 
        }                       
    }
    
    public function uploadLogoRodapeDireitoAction()
    {
        //desabilita layout
        $this->getHelper('layout')->disableLayout();        
        
        
        /** Cria o diretorio do arquivo caso não exista*/
        $diretorioFiles = realpath(APPLICATION_PATH . "/../public/img/logos_certificado") . '/';                        
        $diretorioFiles .= 'rodape';
        if(!is_dir($diretorioFiles)) {            
            mkdir($diretorioFiles, 0777, true);
        }
                                                        
        $extensoesPermitidas = array('swf', 'gif', 'jpeg', 'jpg', 'png');
        
        $fileUploadLogoTopoEsquerda = $_FILES['fileUploadLogoRodapeDireito'];
        if(!empty($fileUploadLogoTopoEsquerda['name'])) {
            $extensao = explode(".", $fileUploadLogoTopoEsquerda['name']);
            $extensao = $extensao[1];
        } else {
            $extensao = '';
        }                        
        
        if (in_array($extensao, $extensoesPermitidas) == false) {            
            $this->_helper->json->sendJson(array(
                    'tipo' => 'erro',
                    'msg' => 'O arquivo deve ser do tipo SWF/GIF/JPEG/JPG/PNG!',                    
            ));            
        }
        
        if($fileUploadLogoTopoEsquerda['size'] > 10485760) {
            $this->_helper->json->sendJson(array(
                    'tipo' => 'erro',
                    'msg' => 'O arquivo não deve exceder 10 MB!',                    
            ));            
        }
         
        $urlArquivo = $this->_helper->upload->file('fileUploadLogoRodapeDireito', null, $extensoesPermitidas, $diretorioFiles.'/');        
        $diretorio = $diretorioFiles.'/'.$urlArquivo;
        
        $htmlImagem = $this->_getHTMLfotoAlunoComPath($urlArquivo, 'rodape','rodape_direito');
        
        if(!empty($urlArquivo)) {
            $this->_helper->json->sendJson(array(
                    'tipo' => 'sucesso',
                    'msg' => 'Imagem transferida com sucesso!',
                    'urlArquivo' => $urlArquivo,
                    'htmlImagem' => $htmlImagem
            )); 
        } else {
            $this->_helper->json->sendJson(array(
                    'tipo' => 'sucesso',
                    'msg' => 'Algum erro ocorreu durante a transferência da image, contate o administrador do sistema!',                    
            )); 
        }                       
    }
    
    private function _getHTMLfotoAlunoComPath($urlImagem, $pastaLogo = 'cabecalho', $tipo_logo = null) {
        if (!empty($urlImagem)) {
            return '<div>Pré Visualização</div>' .                    
                    '<div style="position:relative;">' . $this->_helper->util->getIMGResizeComPath($urlImagem, '110', '80',null, $pastaLogo) . ''.
                ''.'</div>'.'<input type="button" style="font-size: 12px!important;line-height: 15px!important;padding: 5px 10px!important" class="btn removerImagem" tipo_logo="'.$tipo_logo.'" onClick="removerLogo(this)" value="Remover">';
        }
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
    
    public function gridMatriculaTurmaAction()
    {
        $this->getHelper('layout')->disableLayout();
        
        $id_turma = $this->_getParam('id_turma');                                       
        
        $matriculas = $this->matriculaDbTable->getDataGrid(array('id_turma'=>$id_turma));                                                        
                        
        $this->view->matriculas = $this->_helper->util->utf8Encode($matriculas);                 
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
        
        if($params['tx_tipo_cliente'] == 'F') {
            $params['tx_razao_social'] = null;
            $params['[tx_cnpj'] = null;
        } else {
            $params['tx_nome_cliente'] = null;
            $params['tx_cpf_cliente'] = null;
        }                
        
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
