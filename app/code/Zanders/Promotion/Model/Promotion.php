<?php
/**
 * @category   Zanders
 * @package    Zanders_Promotion
 */

namespace Zanders\Promotion\Model;

use Zanders\Promotion\Model\ResourceModel\Promotion as PromotionResourceModel;
use Zanders\Promotion\Model\ResourceModel\Promotion\CollectionFactory as PromotionCollectionFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;

class Promotion extends AbstractModel
{
    protected $storeManager;
    protected $customerSession;
    protected $promotionCollectionFactory;

    public function __construct(
        Context $context,
        Registry $registry,
        StoreManagerInterface $storeManager,
        Session $customerSession,
        PromotionCollectionFactory $promotionCollectionFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->promotionCollectionFactory = $promotionCollectionFactory;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
    }

    protected function _construct()
    {
        $this->_init(PromotionResourceModel::class);
    }

    /*public function getPromotionByMethod($method, $storeId = false)
    {
        if (!$storeId) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        $customerGroupId = $this->customerSession->getCustomerGroupId();
        $extraCharges = $this->promotionCollectionFactory->create();

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
