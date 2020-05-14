<?php
/**
 * @category   Zanders
 * @package    Zanders_Eshow
 */

namespace Zanders\Eshow\Model;

use Zanders\Eshow\Model\ResourceModel\Eshow as EshowResourceModel;
use Zanders\Eshow\Model\ResourceModel\Eshow\CollectionFactory as EshowCollectionFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;

class Eshow extends AbstractModel
{
    protected $storeManager;
    protected $customerSession;
    protected $eshowCollectionFactory;

    public function __construct(
        Context $context,
        Registry $registry,
        StoreManagerInterface $storeManager,
        Session $customerSession,
        EshowCollectionFactory $eshowCollectionFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->eshowCollectionFactory = $eshowCollectionFactory;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
    }

    protected function _construct()
    {
        $this->_init(EshowResourceModel::class);
    }

    /*public function getEshowByMethod($method, $storeId = false)
    {
        if (!$storeId) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        $customerGroupId = $this->customerSession->getCustomerGroupId();
        $extraCharges = $this->eshowCollectionFactory->create();

        $extraCharges
            ->addFieldToFilter('stores', [
                ['like' => '%' . $storeId . '%'],
                ['like' => '%' . $storeId],
                ['like' => $storeId . '%'],
                ['eq' => $storeId],
                ['eq' => 0]
            ])
            ->addFieldToFilter('customer_groups', [
                ['like' => '%' . $customerGroupId . '%'],
                ['like' => '%' . $customerGroupId],
                ['like' => $customerGroupId . '%'],
                ['eq' => $customerGroupId],
                ['null' => true]
            ])
            ->addFieldToFilter('payment_methods', [
                ['like' => '%' . $method . '%'],
                ['like' => '%' . $method],
                ['like' => $method . '%'],
                ['eq' => $method]
            ])
            ->addFieldToFilter('status', 1);

        return $extraCharges;
    }*/
}
