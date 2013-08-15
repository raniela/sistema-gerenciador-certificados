<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    protected $_config;
    protected $_frontController;

    protected function _initConfig() {
        //carrega application ini com parametros
        $this->_config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini');

        //registra configuracao como global
        Zend_Registry::set("config", $this->_config);
    }

    protected function _initAutoLoad() {
        $autoloader = new Zend_Application_Module_Autoloader(array(
                    'namespace' => '',
                    'basePath' => APPLICATION_PATH . '/modules/default'
                ));
        
        Zend_Controller_Action_HelperBroker::addPrefix('Helper_Action');
        Zend_Controller_Action_HelperBroker::addPath(APPLICATION_PATH . '/Helper/Action', 'Helper_Action');
        
        //print_r($autoloader); die;
        return $autoloader;
    }
    
    protected function _initFrontController() {

        $this->_frontController = Zend_Controller_Front::getInstance();

        //informa o local dos controladores

        $this->_frontController->setControllerDirectory(array(
            'default' => APPLICATION_PATH . '/modules/default/controllers',
            'admin' => APPLICATION_PATH . '/modules/admin/controllers',
            'aluno' => APPLICATION_PATH . '/modules/aluno/controllers',
            'cliente' => APPLICATION_PATH . '/modules/cliente/controllers'
        ));
        
        //nao sei se rola isso:
        $this->_frontController->registerPlugin(new Application_Plugin_ValidarLogin());
        
        //print_r($this->_frontController); die;
        
        //$this->_frontController->setParam('noErrorHandler', true);
    }

    /*protected function _initActionHelpers(){
        
        Zend_Controller_Action_HelperBroker::addPath(APPLICATION_PATH . '/Helper/Action');
        
        Zend_Controller_Action_HelperBroker::addHelper(new Helper_Action_Util());
    }*/
           
    
    public function _initConstants() {
        
        /* Define os valores para campo tipo_usuario */
        define('USUARIO_ADMIN', '1');
        define('USUARIO_CLIENTE', '2');
        define('USUARIO_ALUNO', '3');
    }

    public function run() {

        /*
         * faz um processo que pega o objeto de requisicao e extrai modulo,
         * controller, action e parametros opcionais
         * 
         */
        $this->_frontController->dispatch();
    }

}

