<?php

/*
 * 
 * @author Cesar Augusto Vieira Giovani
 * @date 12/09/2013
 * 
 */

class Application_Model_DbTable_Matricula extends Zend_Db_Table_Abstract {

    protected $_name = 'matricula';
    protected $_primary = 'id_matricula';

    public function getDataGrid($params = null) {
        //obj select
        $select = $this->getDefaultAdapter()->select();
        //from contato
        $select->from(array('m' => $this->_name));

        //join 
        $select->joinInner(array('tu' => 'turma'), 'tu.id_turma = m.id_turma', array('dt_inicio_treinamento','dt_termino_treinamento'));
        $select->joinInner(array('tr' => 'treinamento'), 'tr.id_treinamento = tu.id_treinamento', array('tx_nome_treinamento'));
        $select->joinInner(array('a' => 'aluno'), 'a.id_aluno = m.id_aluno', array('tx_nome_aluno','tx_cpf'));

        //ordenacao
        $select->order('tx_nome_aluno');

        //filtros do formulario
        if (!empty($params['nome_treinamento'])) {
            $select->where("tr.tx_nome_treinamento LIKE '%{$params['nome_treinamento']}%'");
        }
        
        if (!empty($params['tx_nome_aluno'])) {
            $select->where("a.tx_nome_aluno LIKE '%{$params['tx_nome_aluno']}%'");
        }
        
        if (!empty($params['tx_cpf'])) {
            $select->where("a.tx_cpf = '{$params['tx_cpf']}'");
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
        
        if (!empty($params['id_matricula'])) {
            $select->where("m.id_matricula = '{$params['id_matricula']}'");
        }

        return $select->query()->fetchAll();
    }

}

?>