<?php

/*
 * @author raniela.carvalho
 * @date 17/07/2013
 * 
 */

class Admin_IndexController extends Zend_Controller_Action {

    public function init() {        
    }

    public function indexAction() {

        $this->view->titulo = "Módulo Administrativo do Sistema de Certificados";

        $dataTurmasVencimento = new DateTime(date('Y-m-d'));
        $dataTurmasVencimento->modify("-10 months");
        $dataVencimentoFinal = $dataTurmasVencimento->format('Y-m-d');        
        $dataTurmasVencimento->modify("-2 months");
        $dataVencimentoInicial = $dataTurmasVencimento->format('Y-m-d');
        
        $turmaDbTable = new Application_Model_DbTable_Turma();
        
        //Buscas as turmas que estão completando 1 Ano 
        $turmasVencidas = $turmaDbTable->getDataTreinamentoVencidos(array('data_vencimento_inicial'=>$dataVencimentoInicial, 'data_vencimento_final' => $dataVencimentoFinal));
        
        $matriculaDbTable = new Application_Model_DbTable_Matricula();
        foreach ($turmasVencidas as $indice => $turma ) {
            $clientes = $matriculaDbTable->getAlunosToTurmasVencidas(array('id_turma'=>$turma['id_turma']));
            $turmasVencidas[$indice]['clientes'] = $clientes;
        }
        
        $this->view->turmasVencidas = $this->_helper->util->utf8Encode($turmasVencidas);
    }

}

