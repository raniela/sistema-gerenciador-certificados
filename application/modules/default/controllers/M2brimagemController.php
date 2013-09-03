<?php

class M2brimagemController extends Zend_Controller_Action
{

	public function init()
	{
		include_once('m2brimagem/m2brimagem.class.php');
	}

    public function resizeAction()
    {
    	/*********************************************************************/
    	/** ATENCAO, NO LOGO DO SISTEMA � PREENCHIDO A SESSION['MBR2_PATH']*/
    	/*********************************************************************/
    	/** adicoes de barra por causa do linux e windows*/
        if(empty($_SESSION['MBR2_PATH'])){
			$_SESSION['MBR2_PATH'] = realpath(APPLICATION_PATH . '/../public/img/logos_certificado/');

			if($_SESSION['MBR2_PATH']{strlen($_SESSION['MBR2_PATH']) - 1} != '/'){
				$_SESSION['MBR2_PATH'] .= '/';
			}
		}
                $path = $this->_getParam('path');
		$arquivo = $this->_getParam('file');
		$realPath = $_SESSION['MBR2_PATH'];
                
                if(!empty($path)) {                    
                    $realPath .= $path.'/';
                }
                
		$largura = $this->_getParam('width');
                $altura = $this->_getParam('height');

		$partes = explode('.' , $arquivo);
		$tam = sizeof($partes);
		$imagem_redimensionada = '';
		for($i = 0; $i < $tam - 1; $i++){
			$imagem_redimensionada .= $partes[$i];
		}
		$imagem_redimensionada .= '_' . $largura. '_x_' . $altura . '.' . $partes[$tam - 1];
		
		
    	//verifica se existe a imagem com as dimensoes especificadas, se n�o existir ele cria e exibe, sen�o s� exibe
		if(!file_exists($realPath.$imagem_redimensionada)){
			$oImg = new m2brimagem($realPath.$arquivo);
	        $valida = $oImg->valida();
	        if ($valida == 'OK') {
	            $oImg->redimensiona($largura, $altura, 'fill');
	            $oImg->grava($realPath.$imagem_redimensionada);
	            $oImg->grava();
	        } else {
	            die($valida);
	        }
	        exit;
		}else{
			$file = $realPath.$imagem_redimensionada;
			header('Content-Description: File Transfer');
		    header('Content-Type: application/octet-stream');
		    header('Content-Disposition: attachment; filename='.basename($file));
		    header('Content-Transfer-Encoding: binary');
		    header('Expires: 0');
		    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		    header('Pragma: public');
		    header('Content-Length: ' . filesize($file));
		    ob_clean();
		    flush();
		    readfile($file);
		    exit;
		}
    }

}
