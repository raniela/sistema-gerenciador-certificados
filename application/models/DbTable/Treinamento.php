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

}

?>
