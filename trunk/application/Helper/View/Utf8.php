<?php 

class Core_View_Helper_Utf8 extends Zend_View_Helper_HtmlElement
{
	
	private $_data;
	
	public function utf8($data)
	{
		$this->_data = $data;
		return $this;
	}
	
	public function encode()
	{
		return $this->_makeArray($this->_data, 'utf8_encode');
	}
	
	public function decode()
	{
		return $this->_makeArray($this->_data, 'utf8_decode');
	}
	
    public function _makeArray($input, $function)
    {
        $output = null;
        if (is_array($input)) {
            if (count($input)) {
                foreach ($input as $k => $val) {
                    $output[$k] = $this->_makeArray($val, $function);
                }
            } else {
                return $input;
            }
        } else {
            return $function($input);
        }
        return $output;
    }

}
