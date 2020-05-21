<?php
class Zanders_Elliott_Model_Elliott_Queryturnaround extends Zanders_Elliott_Model_Elliott_Abstract
{
	protected $_elliotService = '/QueryTurnaround.asmx?wsdl';


	public function getItemNumberFromUpc($upc){

		$data = $this->ExecuteQuery(array("query"=>"ITEM_NO FROM IMITMFIL WHERE ITEM_NOTE_1 = '".$upc."'","numberOfRecords"=>0));

		if($data->ExecuteQueryResult->ReturnCode=='0'){
			$xml = simplexml_load_string($data->ExecuteQueryResult->Data->any);
			return trim($xml->NewDataSet->Data->ITEM_NO);
		}else{
			return false;
		}
	}
}