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
        $select->joinInner(array('tr' => 'treinamento'), 'tr.id_treinamento = tu.id_treinamento', array('tu.id_turma', 'tr.tx_nome_treinamento', 'tu.dt_inicio_treinamento', 'tu.dt_termino_treinamento','tr.nr_carga_horaria','tr.tx_nome_instrutor','tr.tx_descricao'));

        //ordenacao
        $select->order('dt_inicio_treinamento DESC');

        //filtros do formulario
        if (!empty($params['nome_treinamento'])) {
            $select->where("tr.tx_nome_treinamento LIKE '%{$params['nome_treinamento']}%'");
        }

        if (!empty($params['data_inicial']) && empty($params['data_final'])) {
            $select->where("tu.dt_inicio_treinamento >= '{$params['data_inicial']}'");
        }

        if (!empty($params['data_final']) && empty($params['data_inicial'])) {
            $select->where("tu.dt_termino_treinamento <= '{$params['data_final']}' OR tu.dt_inicio_treinamento <= '{$params['data_final']}'");
        }

        if (!empty($params['data_inicial']) && !empty($params['data_final'])) {
            $select->where("(tu.dt_inicio_treinamento >= '{$params['data_inicial']}' AND tu.dt_inicio_treinamento <= '{$params['data_final']}') OR (tu.dt_termino_treinamento >= '{$params['data_inicial']}')");
        }

        if (!empty($params['id_turma'])) {
            $select->where("tu.id_turma = '{$params['id_turma']}'");
        }
        //die($select);
        return $select->query()->fetchAll();
    }
    
    public function getDataTreinamentoVencidos($params = null) {
        //obj select
        $select = $this->getDefaultAdapter()->select();
        //from contato
        $select->from(array('tu' => $this->_name));

        //join 
        $select->joinInner(array('tr' => 'treinamento'), 'tr.id_treinamento = tu.id_treinamento', array('tu.id_turma', 'tr.tx_nome_treinamento', 'tu.dt_inicio_treinamento', 'tu.dt_termino_treinamento','tr.nr_carga_horaria','tr.tx_nome_instrutor','tr.tx_descricao'));

        //ordenacao
        $select->order('tx_nome_treinamento');

        
        if (!empty($params['data_vencimento_inicial'])) {
            $select->where("tu.dt_termino_treinamento >= '{$params['data_vencimento_inicial']}'");
        }
        
        if (!empty($params['data_vencimento_final'])) {
            $select->where("tu.dt_termino_treinamento <= '{$params['data_vencimento_final']}'");
        }
        
        //die($select);
        return $select->query()->fetchAll();
    }

}

?>