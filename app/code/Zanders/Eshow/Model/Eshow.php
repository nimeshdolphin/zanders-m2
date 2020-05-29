<?php
/**
 * @category   Zanders
 * @package    Zanders_Eshow
 */

namespace Zanders\Eshow\Model;

use Zanders\Eshow\Model\ResourceModel\Eshow as EshowResourceModel;
use Zanders\Eshow\Model\ResourceModel\Eshow\CollectionFactory as EshowCollectionFactory;
use Zanders\Eshow\Model\ResourceModel\EshowReceivedItem\CollectionFactory as EshowReceivedItemCollectionFactory;
use Zanders\Eshow\Model\ResourceModel\EshowPurchasedItem\CollectionFactory as EshowPurchasedItemCollectionFactory;
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
    protected $eshowReceivedItemCollectionFactory;
    protected $eshowPurchasedItemCollectionFactory;

    public function __construct(
        Context $context,
        Registry $registry,
        StoreManagerInterface $storeManager,
        Session $customerSession,
        EshowCollectionFactory $eshowCollectionFactory,
        EshowReceivedItemCollectionFactory $eshowReceivedItemCollectionFactory,
        EshowPurchasedItemCollectionFactory $eshowPurchasedItemCollectionFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->eshowCollectionFactory = $eshowCollectionFactory;
        $this->eshowReceivedItemCollectionFactory = $eshowReceivedItemCollectionFactory;
        $this->eshowPurchasedItemCollectionFactory = $eshowPurchasedItemCollectionFactory;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
    }

    protected function _construct()
    {
        $this->_init(EshowResourceModel::class);
    }

    public function getReceiveItems()
    {
            
        $this->eshowReceivedItemCollectionFactory = $this->eshowReceivedItemCollectionFactory->create();
        $this->eshowReceivedItemCollectionFactory->addFieldToFilter('show_id', $this->getId());
        return $this->eshowReceivedItemCollectionFactory;
    }

    public function getPurchaseItems()
    {
        $this->eshowPurchasedItemCollectionFactory = $this->eshowPurchasedItemCollectionFactory->create();
        $this->eshowPurchasedItemCollectionFactory->addFieldToFilter('show_id', $this->getId());
        return $this->eshowPurchasedItemCollectionFactory;
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
