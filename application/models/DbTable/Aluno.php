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
        
        //join 
        $camposCliente = array('tx_cliente' => new Zend_Db_Expr("CASE WHEN c.tx_nome_fantasia IS NOT NULL THEN c.tx_nome_fantasia WHEN c.tx_razao_social IS NOT NULL THEN c.tx_razao_social ELSE c.tx_nome END"));
        $select->joinInner(array('c' => 'cliente'), 'a.id_cliente = c.id_cliente',$camposCliente);
        
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
    
    //Método para buscar os alunos de um determinado cliente,
    //utilizado para realizar a matricula por grupo de alunos de um cliente
    public function getAlunosCliente($params = null)
    {
        //obj select
        $select = $this->getDefaultAdapter()->select();
        
        //from contato
        $select->from(array('a' => $this->_name), array('id_aluno','tx_nome_aluno','tx_cpf'));                                                
        
        //ordenacao
        $select->order('a.tx_nome_aluno');
        $select->order('a.tx_cpf');
        
        //Fitro do paramêtro do método
        if(!empty($params['id_cliente'])) {
            $select->where("a.id_cliente = '{$params['id_cliente']}'");
        }                                                       
        
        return $select->query()->fetchAll();
    }
}