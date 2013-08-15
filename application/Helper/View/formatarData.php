<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of formatarData
 *
 * @author raniela.carvalho
 */
class Helper_View_formatarData extends Zend_View_Helper_HtmlElement {

    public function formatarData($data){
        
         return implode('/', array_reverse(explode("-", $data)));
        
    }
}
