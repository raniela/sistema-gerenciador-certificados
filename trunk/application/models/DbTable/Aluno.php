<?php
/*
 * @author Cesar Augusto Vieira Giovani
 * @date 21/08/2013
 */
class Application_Model_DbTable_Aluno extends Zend_Db_Table_Abstract{
    
    protected $_name = 'aluno';
    protected $_primary = 'id_aluno';                    
}