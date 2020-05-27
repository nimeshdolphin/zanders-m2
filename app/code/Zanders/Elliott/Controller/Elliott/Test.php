<?php
/**
 * @category   Zanders
 * @package    Zanders_Elliott
 */

namespace Zanders\Elliott\Controller\Elliott;

class Test extends \Magento\Framework\App\Action\Action
{
    public function execute()
    {
        $customerElliott = new \Zanders_Elliott_Model_Elliott_Customer();
        $customerInfo = $customerElliott->GetCustomerInfo(array('CustomerNo' => '396152', 'GetCustomerData' => "Y"));

        //var_dump($customerInfo);

        $customerXml = simplexml_load_string($customerInfo->GetCustomerInfoResult->CustomerInfo->any);

        //var_dump($customerXml);

        $termsCls = new \Zanders_Elliott_Model_Elliott_Customer();
        $terms = $termsCls->getTermsList();

        var_dump($terms[trim($customerXml->NewDataSet->ARCUSFIL->CUS_TERMS_CD)]);
        echo 'DONE';
        exit;
    }
}
