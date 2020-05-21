<?php
class Zanders_Elliott_Model_Elliott_Customer extends Zanders_Elliott_Model_Elliott_Abstract
{
	protected $_elliotService = '/CustomerInquiry.asmx?wsdl';

	public function getTermsList(){
		$terms = $this->GetARCodes(array('GetTERMS'=>'Y'));
		$termsxml = simplexml_load_string($terms->GetARCodesResult->ARCodes->any);
		$termsarray = array();
		foreach($termsxml->NewDataSet->TERMS as $term){
			$termsarray[trim($term->TERMS_CD)] = trim($term->TERMS_CD_DESC);
		}
		return $termsarray;
	}

}