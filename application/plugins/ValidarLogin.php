<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ValidarLogin
 *
 * @author
 */
class Application_Plugin_ValidarLogin extends Zend_Controller_Plugin_Abstract {

    //é semrpre disparado qdo executada uma action
    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        //obj auth
        $auth = Zend_Auth::getInstance();

        $usuario = $auth->getIdentity();
        
        //se não é a modulo de login (default)
        if ($request->getModuleName() != 'default') {
            //se o usuário não está logado
            if (!$auth->hasIdentity()) {
                //print_r($auth->getIdentity()); die;
                //renderizo a controller de login e a action index
                $request->setModuleName('default');
                $request->setControllerName('index');
                $request->setActionName('index');
            }
            if ($auth->hasIdentity()) {
                $this->redirecionaUsuario($usuario, $request);
            }
        } elseif ($request->getModuleName() == 'default' && $auth->hasIdentity()) {
            if ($request->getControllerName() == 'index' && $request->getActionName() == 'sair') {
                return null;
            } else {
                $this->redirecionaUsuario($usuario, $request);
            }
        }
    }

    public function redirecionaUsuario($usuario, $request) {
        if ($usuario->tx_tipo_usuario == USUARIO_ADMIN && $request->getControllerName() == 'index') {
            $request->setModuleName('admin');
            $request->setControllerName('index');
            $request->setActionName('index');
        } elseif ($usuario->tx_tipo_usuario == USUARIO_ALUNO && $request->getControllerName() == 'index') {
            $request->setModuleName('aluno');
            $request->setControllerName('index');
            $request->setActionName('index');
        } elseif ($usuario->tx_tipo_usuario == USUARIO_CLIENTE && $request->getControllerName() == 'index') {
            $request->setModuleName('cliente');
            $request->setControllerName('index');
            $request->setActionName('index');
        }
    }

}
