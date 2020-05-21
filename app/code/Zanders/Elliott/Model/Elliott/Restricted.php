<?php
class Zanders_Elliott_Model_Elliott_Restricted extends Zanders_Elliott_Model_Elliott_Abstract
{
	protected $_elliotService = '/ElirstimService.asmx?wsdl';

	protected $_callfororder = null;

	protected $_callbutship = null;

	public function getResriction($itemnumber,$cust_number,$state='',$zip='',$city='',$forceupdate=false){
		if(Mage::helper('sports')->getConfigData('sports/elliott/restictedproduct')){
			if(in_array($itemnumber, $this->getCallToOrder())){
				return "Pick-Up Only- Please Call To Order";
			}
			if(in_array($itemnumber, $this->getCallButShip())){
			    return "Please Call To Order";
			}
			$restcache = Mage::getModel('sports/restrictions')->load($cust_number.'-'.$itemnumber,'sku_customer_number');
			$response = false;
			$keepfor = 259200;
			if($restcache->getId()){
				if($restcache->getRestrictedFlag() != ''){
					$response = $restcache->getRestrictedAttribute();
				}else{
					$keepfor = 1296000;
				}
			}
			$currenttime = Mage::getModel('core/date')->timestamp();
      $timeDiff = 0;
      if ($restcache->getId()) {
        $restCacheTimeStamp = ($restcache->getId() ? strtotime($restcache->getTime()) : null);
        $timeDiff = $currenttime - $restCacheTimeStamp;
      }
			if(!$restcache->getId() || $timeDiff >= $keepfor || $forceupdate){
				$iteminfo = array('ITEM_NO'=>$itemnumber,'CUS_NO'=>$cust_number,'CUS_CITY'=>$city,'CUS_ST'=>$state,'CUS_ZIP'=>$zip);

				$judgement = $this->FindDealerRestrictedItem($iteminfo);

				if($judgement->FindDealerRestrictedItemResult->RstFlag != ''){
					$response = $judgement->FindDealerRestrictedItemResult->RstAttrib;
				}else{
					$response = false;
				}
				try {
					$restcache->setSkuCustomerNumber($cust_number.'-'.$itemnumber)
						  ->setRestrictedFlag(trim($judgement->FindDealerRestrictedItemResult->RstFlag))
						  ->setRestrictedAttribute(trim($judgement->FindDealerRestrictedItemResult->RstAttrib))
						  ->setTime($currenttime)
						  ->save();
				}catch (Zend_Db_Statement_Exception $e){

				}
			}

			return $response;
		}else{
			return false;
		}
	}

	public function getCallToOrder(){
		if(is_null($this->_callfororder)){
			$this->_callfororder = explode(',',Mage::helper('sports')->getConfigData('sports/calltoorder/list'));
		}
		return $this->_callfororder;
	}

	public function getCallButShip(){
	    if(is_null($this->_callbutship)){
	        $this->_callbutship = explode(',',Mage::helper('sports')->getConfigData('sports/calltoorder/justcall'));
	    }
	    return $this->_callbutship;
	}
}
