<?php

/*
 * 
 * @author raniela.carvalho
 * @date 04/09/2013
 * 
 */

class Application_Model_DbTable_Treinamento extends Zend_Db_Table_Abstract {

    protected $_name = 'treinamento';
    protected $_primary = 'id_treinamento';

    public static function getAutoCompleteTreinamentos($key = null) {

        $db = Zend_Db_Table::getDefaultAdapter();

        $query = "SELECT DISTINCT id_treinamento, tx_nome_treinamento FROM treinamento ";

        $data = $db->fetchAll($query);

        $str = array();
        foreach ($data as $dt) {
            $str[] = array(
                'id' => $dt['id_treinamento'],
                'value' => $dt['tx_nome_treinamento']
            );
        }

        return $str;
    }

    public function getDataGrid($params = null) {
        //obj select
        $select = $this->getDefaultAdapter()->select();

        //from contato
        $select->from(array('t' => $this->_name));

        //ordenacao
        $select->order('t.tx_nome_treinamento');

        //print_r($select); die;
        
        //limit de 100 registros para não sobrecarregar a listagem
        if (!empty($params['limit'])) {
            $select->limit($params['limit']);
        } else {
            $select->limit(100);
        }

        //filtros do formulario
        if (!empty($params['tx_nome'])) {
            $select->where("UPPER(t.tx_nome_treinamento) LIKE UPPER('%{$params['tx_nome']}%')");
        }

        //die($select);
        return $select->query()->fetchAll();
    }

}

?>
