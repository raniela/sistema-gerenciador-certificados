<?php
/*
 * @author Cesar Augusto Vieira Giovani
 * @date 05/09/2013
 */

class Application_Model_DbTable_Certificado extends Zend_Db_Table_Abstract {

    protected $_name = 'certificado';
    protected $_primary = 'id_certificado';
    
    //Método para buscar os clientes de acordo com os filtros da tela de buscar
    //Também pode buscar um cliente especifico passando o id_cliente
    public function getDataGrid($params = null)
    {
        //obj select
        $select = $this->getDefaultAdapter()->select();
        
        //from contato
        $select->from(array('c' => $this->_name));                                                
        
        //ordenacao
        $select->order('c.tx_nome_modelo');
        $select->order('c.tx_titulo');
        
        //limit de 100 registros para não sobrecarregar a listagem
        if(!empty($params['limit'])) {
            $select->limit($params['limit']);
        } else {
            $select->limit(100);
        }
        
        //filtros do formulario
        if(!empty($params['id_certificado'])) {
            $select->where("c.id_certificado = '{$params['id_certificado']}'");
        }                                       
        
        if(!empty($params['tx_nome'])) {
            $select->where("UPPER(c.tx_nome_modelo) LIKE UPPER('%{$params['tx_nome_modelo']}%')");
        }
        
        if(!empty($params['tx_razao_social'])) {
            $select->where("UPPER(c.tx_titulo) LIKE UPPER('%{$params['tx_titulo']}%')");
        }               
        
        //die($select);
        return $select->query()->fetchAll();
    }
    
}
