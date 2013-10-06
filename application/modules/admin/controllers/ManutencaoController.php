<?php
/*
 * 
 * @author Cesar Augusto Vieira Giovani
 * @date 30/09/2013
 * 
 */
class Admin_ManutencaoController extends Zend_Controller_Action {
    
    public function init() {                                                                        
        $this->view->menu = 'manutencao';
    }


    public function formBackupAction() {
        $this->view->titulo = "Realizar Backup da Base de Dados";
    }
    
//    UPDATE user SET Password=PASSWORD('123456') WHERE User='root';
//    FLUSH PRIVILEGES;
    public function backupAction() {
        //desabilita layout
        $this->getHelper('layout')->disableLayout();
                        
        $nomeArquivo = time().".sql";                               

        $diretorio = realpath(APPLICATION_PATH . "/../public/files/BACKUP") . "/" . $nomeArquivo;
                        
        $comandoDump = "C:\\xampp\\mysql\\bin\\mysqldump.exe -u root -p123456  -h 127.0.0.1 sgc > ".$diretorio;
        
        exec($comandoDump);       
        
        $this->view->url = $nomeArquivo;                
    }
    
    public function formRestaurarAction() {
        $this->view->titulo = "Restaurar Backup da Base de Dados";
    }
    
    public function uploadBackupAction() {
    
        //desabilita layout
        $this->getHelper('layout')->disableLayout();
        $this->getHelper('viewRenderer')->setNoRender();        
        
        /** Cria o diretorio do arquivo caso não exista*/
        $diretorioFiles = realpath(APPLICATION_PATH . "/../public/files") . '/';                        
        $diretorioFiles .= 'RESTAURAR';
        if(!is_dir($diretorioFiles)) {            
            mkdir($diretorioFiles, 0777, true);
        }
                                
        $extensoesPermitidas = array('sql');
        $urlArquivo = $this->_helper->upload->file('fileUploadBackup', null, $extensoesPermitidas, $diretorioFiles.'/');
        $diretorio = $diretorioFiles.'/'.$urlArquivo;
        
        if($diretorio) {
            $comandoDump = "C:\\xampp\\mysql\\bin\\mysql.exe -u root -p12345 -h 127.0.0.1 sgc < ".$diretorio;        
            exec($comandoDump);

            $this->_helper->json->sendJson(array(
                'tipo' => 'sucesso',
                'msg' => 'Backup retaurado com sucesso!',
            ));
        } else {
            $this->_helper->json->sendJson(array(
                'tipo' => 'sucesso',
                'msg' => 'Algum erro ocorreu, contate o administrador do sistema!',
            ));
        }                                    
    }
}

?>
