<?php

class Zend_Controller_Action_Helper_Upload extends Zend_Controller_Action_Helper_Abstract
{
    /**
     *
     * @var Zend_File_Transfer_Adapter_Http
     */
    private $_fileTransfer;
    /**
     * path do destino do arquivo
     * @var string
     */
    private $_destination;

    public function __construct()
    {
        /* path onde serÃ¡ colocadas os arquivo upados */
        $this->_destination = realpath(APPLICATION_PATH . "/../public/files") . '/';
		//die($this->_destination);
    }

    /**
     *
     * @param Zend_File_Transfer_Adapter_Http $fileTransfer
     */
    public function setFileTransfer(Zend_File_Transfer_Adapter_Http $fileTransfer)
    {
        $this->_fileTransfer = $fileTransfer;
    }

    /**
     *
     * @return Zend_File_Transfer_Adapter_Http
     */
    public function getFileTransfer()
    {
        if (null === $this->_fileTransfer) {
            $this->setFileTransfer(new Zend_File_Transfer_Adapter_Http());
        }
        return $this->_fileTransfer;
    }

    public function file($files, $replace = null, $extensoesPermitidas = null, $destination = null)
    {

        /* verifica se a imagem fui upada senao retorna null */
        if ($this->getFileTransfer()->isUploaded($files)) {
            /* seta o destino do upload */
            if(!empty($destination)){
                $this->_destination = $destination;
            }
            $this->getFileTransfer()->setDestination($this->_destination);

            if(empty($extensoesPermitidas)){
                $extensoesPermitidas = array('swf', 'gif', 'jpeg', 'jpg', 'png','flv', 
                    'avi', 'mpeg', 'wmv', 'wma', 'mov','mp3', 'wma','ret','ced','doc','docx','xls','xlsx','csv','pdf','txt','rar','zip');
            }
            
            /* validacao de tamanho e extensao */
            $this->getFileTransfer()
            ->addValidator('Size', false, array('max' => '100Mb'))
            ->addValidator('Extension', false, $extensoesPermitidas);

            /* validando */
            if (!$this->getFileTransfer()->isValid($files)) {
                /* tratamento da validacao */
                die('Arquivo Inv&aacute;lido');
            }

            try {
                /* subindo o arquivo */
                $this->getFileTransfer()->receive($files);
                /* renomeando  */
                return $this->_rename($files, $replace);
            } catch (Zend_File_Transfer_Exception $exc) {
                throw new Exception('Erro ao enviar arquivo: ' . $exc->getMessage());
            }
        }

        return (!empty($replace)) ? $replace : null;
    }

	public function multiFiles()
    {
		if ($this->getFileTransfer()->isUploaded()) {
			/* seta o destino do upload */
			$this->getFileTransfer()->setDestination($this->_destination);

			/* validacao de tamanho e extensao */
			$this->getFileTransfer()
			->addValidator('Size', false, array('max' => '100Mb'));
			//->addValidator('Extension', false, array('flv', 'avi', 'mpeg', 'wmv', 'wma', 'mov'));

			/* validando */
			if (!$this->getFileTransfer()->isValid()) {
				/* tratamento da validacao */
				throw new Exception('Arquivo Inv&aacute;lido: ' . implode('; ', $this->_fileTransfer->getMessages()));
			}

			try {
				/* subindo o arquivo */
				$this->getFileTransfer()->receive();
						
				/* pega o nome e o path da imagem a ser renomeada */
				$oldnames = $this->getFileTransfer()->getFileName();
				$newnames = array(); 
				foreach($oldnames as $oldname){
				
					@$newname = md5(rand()) . '.' . array_pop(explode('.', $oldname));
					$newnames[] = $newname;
					/* renomeia a imagem */
					rename($oldname, $this->_destination . $newname);
				}
					/* retorna novo nome */
				return $newnames;			
			} catch (Zend_File_Transfer_Exception $exc) {
				throw new Exception('Erro ao enviar arquivo: ' . $exc->getMessage());
			}
		}
		return array();
    }

	
    /**
     * Faz upload de uma imagem
     * @param string $file nome do input file
     * @param int $width largura para validacao
     * @param int $height altura para validacao
     * @return string nome da imagem upada
     */
    public function image($files, $replace = null)
    {
        /* verifica se a imagem fui upada senao retorna null */
        if ($this->getFileTransfer()->isUploaded($files)) {
            /* seta o destino do upload */
            $this->getFileTransfer()->setDestination($this->_destination);

            /* validacao de tamanho e extensao */
            $this->getFileTransfer()
            ->addValidator('Size', false, array('max' => '2Mb'))
            ->addValidator('Extension', false, array('swf', 'gif', 'jpeg', 'jpg', 'png'));

            /* validando */
            if (!$this->getFileTransfer()->isValid($files)) {
                /* tratamento da validacao */
                throw new Exception('Arquivo Inv&aacute;lido: ' . implode('; ', $this->_fileTransfer->getMessages()));
            }

            try {
                /* subindo o arquivo */
                $this->getFileTransfer()->receive($files);
                /* renomeando  */
                return $this->_rename($files, $replace);
            } catch (Zend_File_Transfer_Exception $exc) {
                throw new Exception('Erro ao subir arquivo: ' . $exc->getMessage());
            }
        }
        return (!empty($replace)) ? $replace : null;
    }

    /**
     * Faz upload de um video
     * @param string $files nome do input file
     * @param string $replace nome do arquivo
     * @return string
     */
    public function video($files, $replace = null)
    {
        /* verifica se a imagem fui upada senao retorna null */
        if ($this->getFileTransfer()->isUploaded($files)) {
            /* seta o destino do upload */
            $this->getFileTransfer()->setDestination($this->_destination);

            /* validacao de tamanho e extensao */
            $this->getFileTransfer()
            ->addValidator('Size', false, array('max' => '100Mb'))
            ->addValidator('Extension', false, array('flv', 'avi', 'mpeg', 'wmv', 'wma', 'mov'));

            /* validando */
            if (!$this->getFileTransfer()->isValid($files)) {
                /* tratamento da validacao */
                throw new Exception('Arquivo Inv&aacute;lido: ' . implode('; ', $this->_fileTransfer->getMessages()));
            }

            try {
                /* subindo o arquivo */
                $this->getFileTransfer()->receive($files);
                /* renomeando  */
                return $this->_rename($files, $replace);
            } catch (Zend_File_Transfer_Exception $exc) {
                throw new Exception('Erro ao subir arquivo: ' . $exc->getMessage());
            }
        }

        return (!empty($replace)) ? $replace : null;
    }

/**
     * Faz upload de um audio
     * @param string $files nome do input file
     * @param string $replace nome do arquivo
     * @return string
     */
    public function audio($files, $replace = null)
    {
        /* verifica se a imagem fui upada senao retorna null */
        if ($this->getFileTransfer()->isUploaded($files)) {
            /* seta o destino do upload */
            $this->getFileTransfer()->setDestination($this->_destination);

            /* validacao de tamanho e extensao */
            $this->getFileTransfer()
            ->addValidator('Size', false, array('max' => '100Mb'))
            ->addValidator('Extension', false, array('mp3', 'wma'));

            /* validando */
            if (!$this->getFileTransfer()->isValid($files)) {
                /* tratamento da validacao */
                throw new Exception('Arquivo Inv&aacute;lido: ' . implode('; ', $this->_fileTransfer->getMessages()));
            }

            try {
                /* subindo o arquivo */
                $this->getFileTransfer()->receive($files);
                /* renomeando  */
                return $this->_rename($files, $replace);
            } catch (Zend_File_Transfer_Exception $exc) {
                throw new Exception('Erro ao subir arquivo: ' . $exc->getMessage());
            }
        }

        return (!empty($replace)) ? $replace : null;
    }

    /**
     * redimenciona uma imagem
     * @param string $file nome da imagem
     * @param int $width largunra a ser redimencionada a imagem em px
     * @param int $height altura a ser redimencionada a imagem em px
     */
    public function resize($file, $width, $height)
    {
        $pathFile = $this->_destination . $file;

        if (file_exists($pathFile) && !empty ($file)) {
            $oImg = new m2brimagem($pathFile);
            $valida = $oImg->valida();
            if ($valida == 'OK') {
                $oImg->redimensiona($width, $height, 'crop');
                $oImg->grava($pathFile);
            } else {
                throw new Exception(__METHOD__  . ' - '. $valida);
            }
        }
    }

    /**
     * verifica se um arquivo existe no servidor
     * @param string $file nome do arquivo
     * @return boolean
     */
    public function fileExists($file)
    {
        $pathFile = $this->_destination . $file;
        return file_exists($pathFile);
    }

    /**
     * Remove arquivos da pasta de destino setada
     * @param array|string $files array de nomes do arquivo ou apenas um nome
     */
    public function remove($files)
    {
        if (is_array($files)) {
            /* percorre o array escluindo os arquivos */
            foreach ($files as $file) {
                @unlink($this->_destination . $file);
            }
        } else if (is_string($files)) {
            /* excluir o arquivo passado */
            @unlink($this->_destination . $files);
        }
    }

    /**
     * renome um arquivo
     * @param string $file nome do input file
     * @return string novo nome do arquivo
     */
    private function _rename($files, $replace = null)
    {
        /* pega o nome e o path da imagem a ser renomeada */
        $oldname = $this->getFileTransfer()->getFileName($files);
        
   
        if (empty($replace)) {
            /* gera um novo nome pra imagem */
        	$explode = explode('.', $oldname);
        	$rand = rand();
            $newname = strtoupper(md5($rand . time()) . '.' . array_pop($explode));
        } else {
            /* remove o arquivo anterior */
            //$this->remove($replace);
            /* seta o nome do arquivo com o nome do arquivo excluido */
            $newname = $replace;
        }
        /* renomeia a imagem */
        
        
        rename($oldname, $this->_destination . $newname);
        /* retorna novo nome */
        return $newname;
    }

    /**
     * verifica se um arquivo foi upado
     * @param string $files nome do arquivo
     * @return boolean
     */
    public function isUploaded($files)
    {
        return $this->getFileTransfer()->isUploaded($files);
    }

}
