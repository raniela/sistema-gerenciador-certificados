<?php
/*
 * @author Cesar Augusto Vieira Giovani
 * @date 21/08/2013
 */
class Application_Model_DbTable_Estado extends Zend_Db_Table_Abstract{
    
    protected $_name = 'estado';
    protected $_primary = 'id_estado';
            
    public function getDataCombo()
    {
    	$data = $this->fetchAll(null, 'tx_descricao');
        $dataCombo = array('' => 'Selecione');
        foreach ($data as $k => $val) {
            $dataCombo[$val['id_estado']] = $val['tx_descricao'] . " (". $val['tx_sigla'] . ")";
        }
        return $dataCombo;
    }
    
}