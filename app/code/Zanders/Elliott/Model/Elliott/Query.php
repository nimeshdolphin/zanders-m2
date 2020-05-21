<?php
class Zanders_Elliott_Model_Elliott_Query extends Zanders_Elliott_Model_Elliott_Abstract
{
	protected $_elliotService = '/ElliottQuery.asmx?wsdl';

	public function query($sql){
		$query = array();
		if(is_array($sql)){
			$id = 1;
			foreach($sql as $q){
				$query['query'.$id]=$q;
				$id++;
			}
		}else{
			$query['query1']=$sql;
		}
		$data = $this->ExecuteQuery($query);

		if($data->ExecuteQueryResult->ReturnCode=='0'){
      		$xml = simplexml_load_string($data->ExecuteQueryResult->Data->any);
      		return $xml;
      	}else{
      		return false;
      	}
	}

	public function getInventory($itemnumber){
		$sql = "SELECT ITEM_NO, ITEM_QTY_ON_HAND, ITEM_QTY_ALLOC, ITEM_PRICE FROM IMITMFIL WHERE ITEM_NO = '" . $itemnumber . "'" ;
      	$result = $this->query($sql);
      	$inv = intval(($result->NewDataSet->query1ResultSet->ITEM_QTY_ON_HAND) - ($result->NewDataSet->query1ResultSet->ITEM_QTY_ALLOC));
      	if($inv<0){
      		$inv = 0;
      	}
      	$item = array('inv'=>$inv,
      				  'price'=>floatval($result->NewDataSet->query1ResultSet->ITEM_PRICE));
      	//Mage::log($result);
      	return $item;
	}

	public function getItemInfo($itemnumber){
		$sql = "SELECT ITEM_NO, ITEM_DESC1, ITEM_DESC2, ITEM_QTY_ON_HAND, ITEM_QTY_ALLOC, ITEM_QTY_ON_ORDER_O, ITEM_PRICE, ITEM_WEIGHT FROM IMITMFIL WHERE ITEM_NO = '" . $itemnumber . "'" ;
      	$result = $this->query($sql);
      	if($result){
	      	$available = intval(($result->NewDataSet->query1ResultSet->ITEM_QTY_ON_HAND) - ($result->NewDataSet->query1ResultSet->ITEM_QTY_ALLOC));
	      	$item = array(
	      			'itemNumber'=>trim($result->NewDataSet->query1ResultSet->ITEM_NO),
	      			'itemDescription'=>trim($result->NewDataSet->query1ResultSet->ITEM_DESC1).' '.trim($result->NewDataSet->query1ResultSet->ITEM_DESC2),
	      			'itemPrice'=>strval($result->NewDataSet->query1ResultSet->ITEM_PRICE),
	      			'itemWeight'=>strval($result->NewDataSet->query1ResultSet->ITEM_WEIGHT),
	      			'numberAvailable'=>$available>=0?$available:0,
	      	);
	      	return $item;
      	}else{
      		return false;
      	}
	}

	public function getItemAdditionalShipping($itemnumber){
		$sql = "SELECT ITEM_NO, ITEM_ROUTING_NO, ITEM_WEIGHT FROM IMITMFIL WHERE ITEM_NO = '" . $itemnumber . "'" ;
      	$result = $this->query($sql);
      	$addcharges = array();
		$addchgsapply = false;
            $additionalcharges = 0.00;
            $powderweight = 0.00;
            $primerweight = 0.00;
            switch(trim($result->NewDataSet->query1ResultSet->ITEM_ROUTING_NO)){
			case 'CT':
				$addchgsapply = true;
				break;
			case 'PA':
				$additionalcharges=1.25;
				break;
			case 'PD':
				$powderweight+=($result->NewDataSet->query1ResultSet->ITEM_WEIGHT);
				break;
			case 'PR':
				$primerweight+=($result->NewDataSet->query1ResultSet->ITEM_WEIGHT);
				break;
			case 'SF':
				$addchgsapply = true;
				break;
			case 'SH':
				$additionalcharges=5.00;
				break;
			case 'W1':
				$additionalcharges=1.25;
				break;
			case 'W2':
				$additionalcharges=2.50;
				break;
			case 'CB':
				$additionalcharges=1.50;
				break;

		}
		$addcharges['addchrgneeded'] = $addchgsapply;
		$addcharges['addchrgs'] = $additionalcharges;
		$addcharges['powder'] = $powderweight;
		$addcharges['primer'] = $primerweight;
      	return $addcharges;
	}

	public function getMultiItemInventory($itemnumbers){
    // ZW1-I95 Empty $itemnumbers produces Invalid argument supplied for foreach error. 
    if (empty($itemnumbers)) {
        return array();
    }
		$sql = $this->query("SELECT ITEM_NO, ITEM_QTY_ON_HAND, ITEM_QTY_ALLOC FROM IMITMFIL WHERE ITEM_NO='" . implode("' OR ITEM_NO='",$itemnumbers) . "'"); //ITEM_NO IN ('" . implode("','",$itemnumbers) . "')");
		$iteminv = array();
    try {
        foreach($sql->NewDataSet->query1ResultSet as $productinv){
            $iteminv[trim($productinv->ITEM_NO)]=intval(($productinv->ITEM_QTY_ON_HAND) - ($productinv->ITEM_QTY_ALLOC));
        }
    } catch (Exception $exception) {
        $exception->getMessage();
        Mage::log('Exception' . $exception->getMessage(), null, 'multiinv.log');
        Mage::log('type', null, 'multiinv.log');
        Mage::log(gettype($sql->NewDataSet->query1ResultSet), null, 'multiinv.log');
        Mage::log('data', null, 'multiinv.log');
        Mage::log($sql->NewDataSet->query1ResultSet, null, 'multiinv.log');
    }
		return $iteminv;
	}

	public function getMultiItemInfo($itemnumbers){
		$sql = "SELECT ITEM_NO, ITEM_DESC1, ITEM_DESC2, ITEM_QTY_ON_HAND, ITEM_QTY_ALLOC, ITEM_QTY_ON_ORDER_O, ITEM_PRICE, ITEM_WEIGHT FROM IMITMFIL WHERE ITEM_NO='" . implode("' OR ITEM_NO='",$itemnumbers) . "'"; //ITEM_NO IN ('" . implode("','",$itemnumbers) . "')" ;
      	$result = $this->query($sql);
      	if($result){
      		$items = array();
      		foreach($result->NewDataSet->query1ResultSet as $product){
		      	$available = intval(($product->ITEM_QTY_ON_HAND) - ($product->ITEM_QTY_ALLOC));
		      	$items[] = array(
		      			'itemNumber'=>strval($product->ITEM_NO),
		      			'itemDescription'=>trim($product->ITEM_DESC1).' '.trim($product->ITEM_DESC2),
		      			'itemPrice'=>strval($product->ITEM_PRICE),
		      			'itemWeight'=>strval($product->ITEM_WEIGHT),
		      			'numberAvailable'=>$available>=0?$available:0,
		      	);
      		}
	      	return $items;
      	}else{
      		return false;
      	}
	}

	public function getOrderInvoices($ordernumber){
		$sql = "SELECT INV_NO FROM CPINVHDR WHERE INV_ORDER_NO = ".$ordernumber;
		$result = $this->query($sql);
		$invoices = array();
		foreach($result->NewDataSet->query1ResultSet as $invs){
			$invoices[] = strval($invs->INV_NO);
		}
		return $invoices;
	}

	public function getCustomerInvoicesByOrder($customer, $ordernumber){
		$sql = "SELECT INV_NO FROM CPINVHDR WHERE ((INV_CUSTOMER_NO ='" . $customer . "') AND (INV_ORDER_NO = '" .  $ordernumber . "')) ORDER BY INV_DATE DESC";
		$result = $this->query($sql);
		$invoices = array();
		foreach($result->NewDataSet->query1ResultSet as $invs){
			$invoices[] = strval($invs->INV_NO);
		}
		return $invoices;
	}

	public function getCustomerInvoicesByDate($customer, $date){
		$sql = "SELECT INV_NO FROM CPINVHDR WHERE ((INV_CUSTOMER_NO ='" . $customer . "') AND (INV_DATE = '" .  str_replace(array('-','/'), array('',''), $date) . "'))";
		$result = $this->query($sql);
		$invoices = array();
		foreach($result->NewDataSet->query1ResultSet as $invs){
			$invoices[] = strval($invs->INV_NO);
		}
		return $invoices;
	}

	public function getCustomerInvoicesByDateRange($customer, $startdate, $enddate){
		$sql = "SELECT INV_NO FROM CPINVHDR WHERE ((INV_CUSTOMER_NO ='" . $customer . "') AND (INV_DATE >= '" .  str_replace(array('-','/'), array('',''), $startdate) . "') AND (INV_DATE <= '" .  str_replace(array('-','/'), array('',''), $enddate) . "'))";
		$result = $this->query($sql);
		$invoices = array();
		foreach($result->NewDataSet->query1ResultSet as $invs){
			$invoices[] = strval($invs->INV_NO);
		}
		return $invoices;
	}

	public function getCustomerInvoicesByDateRangeWithInfo($customer, $startdate, $enddate, $orderby='INV_DATE', $sortdir='DESC'){
		$sql = "SELECT INV_NO,INV_ORDER_NO,INV_AR_REFERENCE,INV_DATE,INV_SHIP_TO_NAME,INV_TOT_SALE_AMT,INV_FRT_AMT,INV_MISC_CHG_AMT,INV_SALES_TAX_AMT_1,INV_SALES_TAX_AMT_2,INV_SALES_TAX_AMT_3 FROM CPINVHDR WHERE ((INV_CUSTOMER_NO ='" . $customer . "') AND (INV_DATE >= '" .  str_replace(array('-','/'), array('',''), $startdate) . "') AND (INV_DATE <= '" .  str_replace(array('-','/'), array('',''), $enddate) . "')) ORDER BY ".$orderby." ".$sortdir;
		$result = $this->query($sql);
		$invoices = array();
		foreach($result->NewDataSet->query1ResultSet as $invs){
			$invoices[] = $invs;
		}
		return $invoices;
	}

	public function getCustomerBackorderList($customernumber, $orderby='ORDER_DATE', $sortdir='DESC'){
		$sql = "SELECT ORDER_NO, ORDER_DATE, ORDER_CUSTOMER_NO,ORDER_BILL_TO_NAME,ORDER_PURCH_ORDER_NO, LINE_ITM_ITEM_NO, LINE_ITM_DESC1, LINE_ITM_DESC2, LINE_ITM_QTY_BCK_ORD FROM CPORDLIN_VIEW WHERE ORDER_TYPE='O' AND (ORDER_SELECTION_CODE = 'C' OR ORDER_SELECTION_CODE = 'S' OR ORDER_SELECTION_CODE = 'X') AND LINE_ITM_QTY_BCK_ORD>0 AND ORDER_MFGING_LOC = '01' AND (LINE_ITM_SELECT_CD='N' OR LINE_ITM_SELECT_CD='S') AND ORDER_CUSTOMER_NO ='".$customernumber."' ORDER BY ".$orderby." ".$sortdir;
		$result = $this->query($sql);
		$items = array();
		foreach($result->NewDataSet->query1ResultSet as $item){
			$items[] = $item;
		}
		return $items;
	}
	
	public function getCustomerInvoicesByOrderTrackInfo($customer, $ordernumber){
		$sql = "SELECT INV_NO FROM CPINVHDR WHERE ((INV_CUSTOMER_NO ='" . $customer . "') AND (INV_ORDER_NO = '" .  $ordernumber . "') AND (INV_DATE >= '". str_replace('-','',(date("Y-m-d", strtotime("-120 day")))) . "')) ORDER BY INV_DATE DESC";
		$result = $this->query($sql);
		$invoices = array();
		foreach($result->NewDataSet->query1ResultSet as $invs){
			$invoices[] = strval($invs->INV_NO);
		}
		return $invoices;
	}
}
