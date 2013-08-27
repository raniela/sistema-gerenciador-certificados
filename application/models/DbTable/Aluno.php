<?php
/*
 * @author Cesar Augusto Vieira Giovani
 * @date 21/08/2013
 */
class Application_Model_DbTable_Aluno extends Zend_Db_Table_Abstract{
    
    protected $_name = 'aluno';
    protected $_primary = 'id_aluno';
    
    //Método para buscar os alunos de acordo com os filtros da tela de buscar
    //Também pode buscar um aluno especifico passando o id_cliente
    public function getDataGrid($params = null)
    {
        //obj select
        $select = $this->getDefaultAdapter()->select();
        
        //from contato
        $select->from(array('a' => $this->_name));                                                
        
        //ordenacao
        $select->order('a.tx_nome_aluno');
        $select->order('a.tx_cpf');
        
        //limit de 100 registros para não sobrecarregar a listagem
        if(!empty($params['limit'])) {
            $select->limit($params['limit']);
        } else {
            $select->limit(100);
        }
        
        //filtros do formulario
        if(!empty($params['id_aluno'])) {
            $select->where("a.id_aluno = '{$params['id_aluno']}'");
        }                                       
        
        if(!empty($params['tx_nome_aluno'])) {
            $select->where("UPPER(a.tx_nome_aluno) LIKE UPPER('%{$params['tx_nome_aluno']}%')");
        }                
        
        if(!empty($params['tx_cpf'])) {
            $select->where("a.tx_cpf = '{$params['tx_cpf']}'");
        }
               
        return $select->query()->fetchAll();
    }
}