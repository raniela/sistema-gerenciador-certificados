<?php

/*
 * 
 * @author raniela.carvalho
 * @date 03/09/2013
 * 
 */

class Application_Model_DbTable_Turma extends Zend_Db_Table_Abstract {

    protected $_name = 'turma';
    protected $_primary = 'id_turma';


    public function getDataGrid($params = null) {
        //obj select
        $select = $this->getDefaultAdapter()->select();
        //from contato
        $select->from(array('tu' => $this->_name));

        //join 
        $select->joinInner(array('tr' => 'treinamento'), 'tr.id_treinamento = tu.id_treinamento');

        //ordenacao
        $select->order('dt_inicio_treinamento DESC');

        //filtros do formulario
        if (!empty($params['nome_treinamento'])) {
            $select->where("tr.tx_nome_treinamento LIKE '%{$params['nome_treinamento']}%'");
        }
        
        if (!empty($params['data_inicial'])) {
            $select->where("tu.dt_inicio_treinamento >= '{$params['data_inicial']}'");
        }
        
        if (!empty($params['data_final'])) {
            $select->where("tu.dt_termino_treinamento <= '{$params['data_final']}'");
        }

        print_r($select->query()->fetchAll()); die;
        
        return $select->query()->fetchAll();
        
        
    }

    
}

?>
