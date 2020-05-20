<?php
/**
 * @category   Zanders
 * @package    Zanders_ProductDescription
 */

namespace Zanders\ProductDescription\Model;

use Zanders\ProductDescription\Model\ResourceModel\ProductDescription as ProductDescriptionResourceModel;
use Zanders\ProductDescription\Model\ResourceModel\ProductDescription\CollectionFactory as ProductDescriptionCollectionFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;

class ProductDescription extends AbstractModel
{
    protected $storeManager;
    protected $customerSession;
    protected $productDescriptionCollectionFactory;

    public function __construct(
        Context $context,
        Registry $registry,
        StoreManagerInterface $storeManager,
        Session $customerSession,
        ProductDescriptionCollectionFactory $productDescriptionCollectionFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->promotionCollectionFactory = $productDescriptionCollectionFactory;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
    }

    protected function _construct()
    {
        $this->_init(ProductDescriptionResourceModel::class);
    }
}
