<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Util
 *
 * @author raniela.carvalho
 */
class Helper_Action_Util extends Zend_Controller_Action_Helper_Abstract {


    public function formatarDataParaInserir($data)
    {
        return implode('-', array_reverse(explode("/", $data)));
    }
    
    /**
     * adiciona utf8 em um array
     * @param <type> $input
     * @return <type>
     */
    public function utf8Encode($input) {
        $output = null;
        if (is_array($input)) {
            if (count($input)) {
                foreach ($input as $k => $val) {
                    $output[$k] = $this->utf8Encode($val);
                }
            } else {
                return $input;
            }
        } else {
            return utf8_encode($input);
        }
        return $output;
    }

    /**
     * remove utf8 em um array
     * @param <type> $input
     * @return <type>
     */
    public function utf8Decode($input) {
        $output = null;
        if (is_array($input)) {
            if (count($input)) {
                foreach ($input as $k => $val) {
                    $output[$k] = $this->utf8Decode($val);
                }
            } else {
                return $input;
            }
        } else {
            return utf8_decode($input);
        }
        return $output;
    }
    
    /**
     * funcao para retirar acentos e passar a frase para minuscula
     * @param string $string
     * @return string
     */
    public function normalizeString($string) {
        $a = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿ??';
        $b = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
        $string = utf8_decode($string);
        $string = strtr($string, utf8_decode($a), $b); //substitui letras acentuadas por &quot;normais&quot;
        //$string = str_replace(&quot; &quot;,&quot;&quot;,$string); // retira espaco
        $string = strtolower($string); // passa tudo para minusculo
        return utf8_encode($string); //finaliza, gerando uma saída para a funcao
    }

    public function removeAcento($string) {
        $a = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿ';
        $b = 'AAAAAAAÇEEEEIIIIDNOOOOOOUUUUYbsaaaaaaaceeeeiiiidnoooooouuuyyby';
        $string = strtr($string, $a, $b); //substitui letras acentuadas por &quot;normais&quot;
        return $string;
    }
    
    /**
     * tranforma uma string em url
     * @param string $string
     * @return string
     */
    public function stringToUrl($string) {
        $string = $this->normalizeString($string);
        $string = str_replace(' ', '-', $string);
        $string = str_replace('"', '', $string);
        return substr($string, 0, 200);
    }
    
    /**
     * reverte o formata de uma data de pt para en ou en para pt
     * @param string $date
     * @return string
     */
    public function reverseDate($date) {
        $arrDate = explode(' ', $date);
        $arrDate[0] = $this->_arrumaData($arrDate[0]);

        $date = $arrDate[0];
        $time = @$arrDate[1];

        if (stripos($date, '/') !== false) {
            return implode('/', array_reverse(explode('/', $date))) . ' ' . $time;
        } else {
            return implode('/', array_reverse(explode('-', $date))) . ' ' . $time;
        }
    }
    
    private function _arrumaData($data) {
        /** caso o dia ou o mês estejam com apenas 1 digito, adiciona o "0" para que fique com 2 digitos */
        $dataExplode = explode('/', str_replace('-', '/', $data));
        $dataProcessada = '';
        foreach ($dataExplode as $parte) {
            if (strlen($parte) == 1) {
                $parte = '0' . $parte;
            }
            if (empty($dataProcessada)) {
                $dataProcessada = $parte;
            } else {
                if (stripos($data, '/')) {
                    $dataProcessada .= '/' . $parte;
                } else {
                    $dataProcessada .= '-' . $parte;
                }
            }
        }
        $data = $dataProcessada;
        return $data;
    }
    
    public function moneyToFloat($vl) {
        $vl = "{$vl}";
        $vl = str_replace('.', '', $vl);
        $vl = str_replace(',', '.', $vl);

        $vetor = explode('.', $vl);

        $pInteiro = $vetor[0];
        $pDecimal = empty($vetor[1]) ? '0' : $vetor[1];

        $pInteiro = (double) $pInteiro;
        $pDecimal = (double) $pDecimal;

        $vl = $pInteiro + ($pDecimal / 100);

        return $vl;
    }
    
    public function floatToMoney($vl, $decimals = 2) {
        return number_format($vl, $decimals, ',', '.');
    }
    
    function urldecodeGet($vl) {
        $vl = str_replace('{1}', '!', $vl);
        $vl = str_replace('{2}', '/', $vl);
        $vl = str_replace('{3}', '(', $vl);
        $vl = str_replace('{4}', ')', $vl);
        $vl = str_replace('{5}', '*', $vl);
        $vl = str_replace('{6}', '%', $vl);
        $vl = str_replace('{7}', '#', $vl);

        $vl = str_replace('{8}', '"', $vl);
        $vl = str_replace('{9}', "'", $vl);
        $vl = str_replace('{10}', '=', $vl);

        $vl = str_replace('{}', ' ', $vl);
        return $vl;
    }
    
    function getIMGResizeComPath($urlImagem, $height = null, $width = null, $alt = null, $path = null) {
        $alt = htmlentities($alt);
        if ($height && $width) {
            return '<img alt="' . $alt . '" title="' . $alt . '" id="' . $alt . '" src="' . Zend_Controller_Front::getInstance()->getBaseUrl() . '/m2brimagem/resize/file/' . $urlImagem . '/width/' . $width . '/height/' . $height . '/path/'. $path . '">';
        } else {
            return '<img alt="' . $alt . '" title="' . $alt . '" id="' . $alt . '" src="' . Zend_Controller_Front::getInstance()->getBaseUrl() . '/public/files/' . $urlImagem . '">';
        }
    }
}
