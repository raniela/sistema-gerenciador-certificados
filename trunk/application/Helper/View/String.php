<?php

class Helper_View_String extends Zend_View_Helper_HtmlElement
{
    private $_string;

    /**
     *
     * @param string $string
     * @return Base_View_Helper_String
     */
    public function string($string)
    {
        $this->_string = $string;
        return $this;
    }

    /**
     * corta a string
     * @param string $length tamanho em q sera cortada
     * @return string string cortada
     */
    public function cut($length = null)
    {
        if (!empty($length)) {
            /* corta a string */
            $substr = substr($this->_string, 0, $length);
            /* se a string original for maior q a cortada adiciona "..." */
            if (strlen($this->_string) > $length) {
                $substr .= '...';
            }
            return $substr;
        }
        return $this->_string;
    }

    /**
     * quebra a linha em string muito grandes
     * @param string $str string
     * @param int $width tamanho em q será quebrada
     * @return string
     */
    public function wordWrap($width = 30)
    {
        $arrStr = explode(' ', $this->_string);
        $newStr = '';
        foreach ($arrStr as $val) {
            if (trim($val) != '') {
                $newStr .= " " . $this->_wordWrap(trim($val), $width);
            }
        }
        return $newStr;
    }
    
    public function wordWrapPDF($width = 30){
        return $this->_wordWrap(trim($this->_string), $width);
    }

    /**
     * complementa a funcao wordWrap
     * @param <type> $str
     * @param <type> $width
     * @return string
     */
    private function _wordWrap($str, $width = 30)
    {
        if (!empty($width)) {
            /* caracter q sera adicionado para quebrar a linha */
            $caracter = "<br />";
            $length = strlen($str);
            /* se a string for maior q o tamanho passado não quebra */
            if ($length > $width) {
                /* numero de vezes q sera quebrada */
                $quebra = $length / $width;
                $ini = 0;
                $fim = $width;
                /* string com os caracteres para quebrar linha adicioandos */
                $nova = '';
                /* gerando nova string */
                for ($i = 0; $i <= intval($quebra); $i++) {
                    if ($i == intval($quebra)) {
                        $nova.= substr($str, $ini, $width);
                    } else {
                        $nova.= substr($str, $ini, $width) . $caracter;
                    }
                    $ini = $fim;
                    $fim = $fim + $width;
                }
                /* retorna string com caracteres adicionados para quebrar linha */
                return $nova;
            } else {
                /* retorna string normal */
                return $str;
            }
        }
        return $str;
    }

    public function geraUrlReescrita(){
    	$texto = strtolower($this->_string);
    	$a = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿ??/ +\'´`="!@#\$£%¢¬&()_§]}º°{[ª^~/:;.,\\|><';
        $b = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyyby  --                              -         ';
        $texto = strtr($texto, $a, $b); //substitui letras acentuadas por &quot;normais&quot;
        $texto  = str_replace('  ', ' ', trim($texto)); /** remove espacos duplos*/
        $texto  = str_replace(' ', '', trim($texto)); /** remove os espacos simples*/
		return $texto;
	}
	
	public function moneyToExtenso() {
		$valor = str_replace('.','',$this->_string);
		$valor = str_replace(',','.',$valor);
		$singular = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
		$plural = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões","quatrilhões");

		$c = array("", "cem", "duzentos", "trezentos", "quatrocentos",	"quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
		$d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta","sessenta", "setenta", "oitenta", "noventa");
		$d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze","dezesseis", "dezesete", "dezoito", "dezenove");
		$u = array("", "um", "dois", "três", "quatro", "cinco", "seis",	"sete", "oito", "nove");

		$z=0;

		$rt = null;

		$valor = number_format($valor, 2, ".", ".");
		$inteiro = explode(".", $valor);
		for($i=0;$i<count($inteiro);$i++)
			for($ii=strlen($inteiro[$i]);$ii<3;$ii++)
				$inteiro[$i] = "0".$inteiro[$i];

		// $fim identifica onde que deve se dar junção de centenas por "e" ou por "," ;)
		$fim = count($inteiro) - ($inteiro[count($inteiro)-1] > 0 ? 1 : 2);
		for ($i=0;$i<count($inteiro);$i++) {
			$valor = $inteiro[$i];
			$rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
			$rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
			$ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

			$r = $rc.(($rc && ($rd || $ru)) ? " e " : "").$rd.(($rd &&
	$ru) ? " e " : "").$ru;
			$t = count($inteiro)-1-$i;
			$r .= $r ? " ".($valor > 1 ? $plural[$t] : $singular[$t]) : "";
			if ($valor == "000")$z++; elseif ($z > 0) $z--;
			if (($t==1) && ($z>0) && ($inteiro[0] > 0)) $r .= (($z>1) ? " de " : "").$plural[$t];
			if ($r) $rt = $rt . ((($i > 0) && ($i <= $fim) &&
	($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
		}

		return($rt ? $rt : "zero");
	}
	
	public function toExtenso() {
		$valor = str_replace('.','',$this->_string);
		$valor = str_replace(',','.',$valor);
		$singular = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
		$plural = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões","quatrilhões");

		$c = array("", "cem", "duzentos", "trezentos", "quatrocentos",	"quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
		$d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta","sessenta", "setenta", "oitenta", "noventa");
		$d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze","dezesseis", "dezesete", "dezoito", "dezenove");
		$u = array("", "um", "dois", "três", "quatro", "cinco", "seis",	"sete", "oito", "nove");

		$z=0;

		$rt = null;

		$valor = number_format($valor, 2, ".", ".");
		$inteiro = explode(".", $valor);
		for($i=0;$i<count($inteiro);$i++)
			for($ii=strlen($inteiro[$i]);$ii<3;$ii++)
				$inteiro[$i] = "0".$inteiro[$i];

		// $fim identifica onde que deve se dar junção de centenas por "e" ou por "," ;)
		$fim = count($inteiro) - ($inteiro[count($inteiro)-1] > 0 ? 1 : 2);
		for ($i=0;$i<count($inteiro);$i++) {
			$valor = $inteiro[$i];
			$rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
			$rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
			$ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

			$r = $rc.(($rc && ($rd || $ru)) ? " e " : "").$rd.(($rd &&
	$ru) ? " e " : "").$ru;
			$t = count($inteiro)-1-$i;
			$r .= $r ? " ".($valor > 1 ? $plural[$t] : $singular[$t]) : "";
			if ($valor == "000")$z++; elseif ($z > 0) $z--;
			if (($t==1) && ($z>0) && ($inteiro[0] > 0)) $r .= (($z>1) ? " de " : "").$plural[$t];
			if ($r) $rt = $rt . ((($i > 0) && ($i <= $fim) &&
	($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
		}

		$retorno = ($rt ? $rt : "zero");
		$retorno = str_replace('centavos','',$retorno);
		$retorno = str_replace('centavo','',$retorno);
		$retorno = str_replace('real','',$retorno);
		$retorno = str_replace('reais','',$retorno);
		
		return trim($retorno);
	}
	
/** retorna o nome do mês de acordo com o número */
	public function getNomMonth(){
		$nr_month = (int)$this->_string;
		$months = array();
		$months[1] = 'Janeiro';
		$months[2] = 'Fevereiro';
		$months[3] = 'Março';
		$months[4] = 'Abril';
		$months[5] = 'Maio';
		$months[6] = 'Junho';
		$months[7] = 'Julho';
		$months[8] = 'Agosto';
		$months[9] = 'Setembro';
		$months[10] = 'Outubro';
		$months[11] = 'Novembro';
		$months[12] = 'Dezembro';
		
		return $months[$nr_month];
	}
	
	/**
     * reverte o formata de uma data de pt para en ou en para pt
     * @param string $date
     * @return string
     */
    public function reverseDate()
    {
    	$date = $this->_string;
        $arrDate = explode(' ', $date);

        $date = $arrDate[0];
        $time = @$arrDate[1];

        if (stripos($date, '/') !== false) {
            return implode('/', array_reverse(explode('/', $date))) . ' ' . $time;
        } else {
            return implode('/', array_reverse(explode('-', $date))) . ' ' . $time;
        }
    }
    
	public function moneyToFloat(){
		$vl = $this->_string;
		$vl = str_replace('.','',$vl);
		return str_replace(',','.',$vl);
	}
	
	public function floatToMoney(){
		$decimals = 2;
		$vl = $this->_string;
		return number_format($vl,$decimals, ',', '.');
	}
	
	public function dateToNumber(){
		$vl = str_replace('/','',$this->_string);
		$vl = str_replace('-','',$vl);
		return (int)$vl;
	}
	
	public function maskCPF(){
		$campo = $this->_string;
		
		//retira formato
		$codigoLimpo = ereg_replace("[' '-./ t]",'',$campo);
		$codigoLimpo = substr($codigoLimpo, strlen($codigoLimpo) - 11, strlen($codigoLimpo));
		// pega o tamanho da string menos os digitos verificadores
		$tamanho = (strlen($codigoLimpo) -2);
		//verifica se o tamanho do código informado é válido		
	 
		// seleciona a máscara para cpf ou cnpj
		$mascara = ($tamanho == 9) ? '###.###.###-##' : '##.###.###/####-##'; 
	 
		$indice = -1;
		for ($i=0; $i < strlen($mascara); $i++) {
			if ($mascara[$i]=='#') $mascara[$i] = $codigoLimpo[++$indice];
		}
		//retorna o campo formatado
		$retorno = $mascara;
	  
		return $retorno;
	}
	
	/** formata um valor com zeros a esquerda */
    public function to_char($zerosAEsquerda){
		$vl = (int)$this->_string;
		for($i = 1; strlen($vl) < $zerosAEsquerda; $i++){
			$vl = "0".$vl;
		}
		return $vl;
	}
	
	//retorna o texto no formato correto para impressão num Rotate de PDF
	//Basicamente substitui os espaços pelo código de espaço &nbsp; pois a biblioteca de PDF não consegue fazer o rotate corretamente
	//se existir espaço no texto
	public function strToRotateTextPDF(){
		return str_replace(' ','&nbsp;',$this->_string);
	}

	/**
	 * 
	 * Converte uma hora em minutos
	 */
	public function horaToMinutos() {
		$horario = $this->_string;
        $arrHorario = explode(':', $horario);
        $min = (60 * (int) $arrHorario[0]) + (int) $arrHorario[1];
        return $min;
    }
}
