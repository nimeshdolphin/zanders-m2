<?php
/**
 * @category   Zanders
 * @package    Zanders_Sports
 */

namespace Zanders\Sports\Controller\Index;

use Magento\Framework\Controller\ResultFactory;

class FflDropship extends \Magento\Framework\App\Action\Action
{
    protected $customerSession;
    protected $customerFactory;
    private $regionCollectionFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory,
        array $data = []
    )
    {
        $this->customerSession = $customerSession;
        $this->customerFactory = $customerFactory;
        $this->regionCollectionFactory = $regionCollectionFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        if ($this->customerSession->isLoggedIn()) {
            try {
                $customer = $this->customerFactory->create();
                $customer->load($this->customerSession->getCustomer()->getId());

                $ffl = str_replace(array(' ', '-', '/', '\\'), '', $this->getRequest()->getParam('fflnumber'));
                $searchinfo = array('SearchShipToInput' => array(
                    'SearchCustomerNo' => '418944',
                    'SearchFieldName' => 'XREF',
                    'SearchValue' => '15634115',
                ));

                $elliottShipToAddresses = new \Zanders_Elliott_Model_Elliott_Shiptoaddresses();
                $info = $elliottShipToAddresses->SearchShipTo($searchinfo);

                if ($info->SearchShipToResult->NoOfSearchResult > 0) {
                    $region = $this->regionCollectionFactory->create()
                        ->addRegionCodeFilter($info->SearchShipToResult->ShipToRecord->shipToRecord->ShipToState)
                        ->addCountryCodeFilter('US')
                        ->getFirstItem()
                        ->toArray();
                    if (!empty($region) && array_key_exists('region_id', $region))
                        $info->SearchShipToResult->ShipToRecord->shipToRecord->ShipToStateCode = $region->getId();
                }
            } catch (Exception $e) {
                $info = $e;
            }
            $result->setData($info);
            return $result;
        }
    }
}
