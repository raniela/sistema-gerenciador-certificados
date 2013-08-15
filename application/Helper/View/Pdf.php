<?php 

class Core_View_Helper_Pdf extends Zend_View_Helper_HtmlElement
{
	public function pdf($content, $nameFile, $save = null, $orientation = 'P')
	{		
		/** conversion HTML => PDF */
		include_once("html2pdf_v4.03_php5/html2pdf_v4.03/html2pdf.class.php");
		try{
			$html2pdf = new HTML2PDF($orientation,'A4','en', false, 'ISO-8859-15');
			$html2pdf->writeHTML($content, false);
			
			if($save === null){
				//die('AQQQQQQQQQQQQQQQQQQQQQQQ');
				//apenas imprime na tela
				$html2pdf->Output($nameFile);
			}else{				
				//print(str_replace('\\','/',realpath('')) . "/public/files/" . $nameFile);
				//die('ELSE');
				$html2pdf->Output(str_replace('\\','/',realpath('')) . "/public/files/" . $nameFile, $save);
			}
		}
		catch(HTML2PDF_exception $e) { echo $e; }
		
		/** redireciona para o arquivo salvo */
		if(!empty($save)){
			print "<script>window.location='" . $this->view->baseUrl('public/files/') . $nameFile . "'</script>";
		}
	}
	
}