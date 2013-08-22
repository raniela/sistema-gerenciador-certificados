<?php
/*
 * @author Cesar Augusto Vieira Giovani
 * @date 19/08/2013
 */

class Application_Model_DbTable_Cliente extends Zend_Db_Table_Abstract {

    protected $_name = 'cliente';
    protected $_primary = 'id_cliente';
    
    //Método para buscar os clientes de acordo com os filtros da tela de buscar
    //Também pode buscar um cliente especifico passando o id_cliente
    public function getDataGrid($params = null)
    {
        //obj select
        $select = $this->getDefaultAdapter()->select();
        
        //from contato
        $select->from(array('c' => $this->_name));                                                
        
        //ordenacao
        $select->order('c.tx_nome');
        $select->order('c.tx_razao_social');
        
        //limit de 100 registros para não sobrecarregar a listagem
        if(!empty($params['limit'])) {
            $select->limit($params['limit']);
        } else {
            $select->limit(100);
        }
        
        //filtros do formulario
        if(!empty($params['id_cliente'])) {
            $select->where("c.id_cliente = '{$params['id_cliente']}'");
        }                                       
        
        if(!empty($params['tx_nome'])) {
            $select->where("UPPER(c.tx_nome) LIKE UPPER('%{$params['tx_nome']}%')");
        }
        
        if(!empty($params['tx_razao_social'])) {
            $select->where("UPPER(c.tx_razao_social) LIKE UPPER('%{$params['tx_razao_social']}%')");
        }
        
        if(!empty($params['tx_cpf'])) {
            $select->where("c.tx_cpf = '{$params['tx_cpf']}'");
        }
        
        if(!empty($params['tx_cnpj'])) {
            $select->where("c.tx_cnpj = '{$params['tx_cnpj']}'");
        }
        
        if(!empty($params['tx_tipo_cliente'])) {
            $select->where("c.tx_tipo_cliente = '{$params['tx_tipo_cliente']}'");
        }        
        
        //die($select);
        return $select->query()->fetchAll();
    }
    
}
