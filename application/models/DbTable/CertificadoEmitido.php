<?php
/*
 * @author Cesar Augusto Vieira Giovani
 * @date 16/09/2013
 */

class Application_Model_DbTable_CertificadoEmitido extends Zend_Db_Table_Abstract {

    protected $_name = 'certificado_emitido';
    protected $_primary = 'id_certificado_emitido';
    
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
        
        if(!empty($params['tx_nome_modelo'])) {
            $select->where("UPPER(c.tx_nome_modelo) LIKE UPPER('%{$params['tx_nome_modelo']}%')");
        }
        
        if(!empty($params['tx_titulo'])) {
            $select->where("UPPER(c.tx_titulo) LIKE UPPER('%{$params['tx_titulo']}%')");
        }               
        
        //die($select);
        return $select->query()->fetchAll();
    }
    
    //Método para buscar os certificados emitidos de acordo com os filtros da tela de buscar
    //Também pode buscar um certificado emitido especifico passando o id_certificado_emitido
    public function getDataGridPesquisar($params = null)
    {
        //obj select
        $select = $this->getDefaultAdapter()->select();
        
        //from contato
        $select->from(array('ce' => $this->_name));                                                
        
        //join 
        $select->joinInner(array('c' => 'certificado'), 'ce.id_certificado = c.id_certificado', array('*'));
        $select->joinInner(array('m' => 'matricula'), 'ce.id_matricula = m.id_matricula', array());
        $select->joinInner(array('t' => 'turma'), 'm.id_turma = t.id_turma', array('id_turma','dt_inicio_treinamento','dt_termino_treinamento'));
        $select->joinInner(array('tr' => 'treinamento'), 't.id_treinamento = tr.id_treinamento', array('*'));
        $select->joinInner(array('a' => 'aluno'), 'm.id_aluno = a.id_aluno', array('*'));
        
        $camposCliente = array('tx_cliente' => new Zend_Db_Expr("CASE WHEN cli.tx_nome_fantasia IS NOT NULL THEN cli.tx_nome_fantasia WHEN cli.tx_razao_social IS NOT NULL THEN cli.tx_razao_social ELSE cli.tx_nome END"));
        $select->joinInner(array('cli' => 'cliente'), 'a.id_cliente = cli.id_cliente', $camposCliente);
        
        //ordenacao
        $select->order('ce.dt_emissao_certificado DESC');
        $select->order('a.tx_nome_aluno');
        
        //limit de 100 registros para não sobrecarregar a listagem
        if(!empty($params['limit'])) {
            $select->limit($params['limit']);
        } else {
            $select->limit(100);
        }
        
        //filtros do formulario
        if(!empty($params['id_certificado_emitido'])) {
            $select->where("ce.id_certificado_emitido = '{$params['id_certificado_emitido']}'");
        }                                       
        
        if(!empty($params['nr_registro_certificado'])) {
            $select->where("ce.nr_registro_certificado = '{$params['nr_registro_certificado']}'");
        }
        
        if (!empty($params['data_inicial']) && empty($params['data_final'])) {
            $select->where("t.dt_inicio_treinamento >= '{$params['data_inicial']}'");
        }

        if (!empty($params['data_final']) && empty($params['data_inicial'])) {
            $select->where("t.dt_termino_treinamento <= '{$params['data_final']}' OR t.dt_inicio_treinamento <= '{$params['data_final']}'");
        }

        if (!empty($params['data_inicial']) && !empty($params['data_final'])) {
            $select->where("(t.dt_inicio_treinamento >= '{$params['data_inicial']}' AND t.dt_inicio_treinamento <= '{$params['data_final']}') OR (t.dt_termino_treinamento >= '{$params['data_inicial']}')");
        }
        
        if(!empty($params['nome_treinamento'])) {
            $select->where("UPPER(tr.tx_nome_treinamento) LIKE UPPER('%{$params['nome_treinamento']}%')");
        }               
        
        if(!empty($params['tx_nome_aluno'])) {
            $select->where("UPPER(a.tx_nome_aluno) LIKE UPPER('%{$params['tx_nome_aluno']}%')");
        }
        
        if(!empty($params['tx_rg'])) {
            $select->where("a.tx_rg = '{$params['tx_rg']}'");
        }
        
        if(!empty($params['tx_cpf'])) {
            $select->where("a.tx_cpf = '{$params['tx_cpf']}'");
        }
        
        if(!empty($params['tx_tipo_cliente'])) {
            $select->where("cli.tx_tipo_cliente = '{$params['tx_tipo_cliente']}'");
        }
        
        if(!empty($params['tx_razao_social'])) {
            $select->where("UPPER(cli.tx_razao_social) LIKE UPPER('%{$params['tx_razao_social']}%')");
        }
        
        if(!empty($params['tx_nome_cliente'])) {
            $select->where("UPPER(cli.tx_nome) LIKE UPPER('%{$params['tx_nome_cliente']}%')");
        }
        
        if(!empty($params['tx_cnpj'])) {
            $select->where("cli.tx_cnpj = '{$params['tx_cnpj']}'");
        }
                
        if(!empty($params['tx_cpf_cliente'])) {
            $select->where("cli.tx_cpf = '{$params['tx_cpf_cliente']}'");
        }
        //die($select);
        return $select->query()->fetchAll();
    }
    
}
