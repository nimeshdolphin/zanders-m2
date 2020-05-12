<?php
/**
 * @category   Zanders
 * @package    Zanders_Promotion
 */

namespace Zanders\Promotion\Model;

use Zanders\Promotion\Model\ResourceModel\TopPromo as TopPromoResourceModel;
use Zanders\Promotion\Model\ResourceModel\TopPromo\CollectionFactory as TopPromoCollectionFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;

class TopPromo extends AbstractModel
{
    protected $storeManager;
    protected $customerSession;
    protected $topPromoCollectionFactory;

    public function __construct(
        Context $context,
        Registry $registry,
        StoreManagerInterface $storeManager,
        Session $customerSession,
        TopPromoCollectionFactory $topPromoCollectionFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->topPromoCollectionFactory = $topPromoCollectionFactory;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
    }

    protected function _construct()
    {
        $this->_init(TopPromoResourceModel::class);
    }

}
