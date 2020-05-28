<?php
/**
 * @category   Zanders
 * @package    Zanders_Eshow
 */

namespace Zanders\Eshow\Model;

use Zanders\Eshow\Model\ResourceModel\EshowReceivedItem as EshowReceivedItemResourceModel;
use Zanders\Eshow\Model\ResourceModel\EshowReceivedItem\CollectionFactory as EshowReceivedItemCollectionFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;

class EshowReceivedItem extends AbstractModel
{
    protected $storeManager;
    protected $customerSession;
    protected $eshowReceivedItemCollectionFactory;

    public function __construct(
        Context $context,
        Registry $registry,
        StoreManagerInterface $storeManager,
        Session $customerSession,
        EshowReceivedItemCollectionFactory $eshowReceivedItemCollectionFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->eshowReceivedItemCollectionFactory = $eshowReceivedItemCollectionFactory;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
    }

    protected function _construct()
    {
        $this->_init(EshowReceivedItemResourceModel::class);
    }
}
