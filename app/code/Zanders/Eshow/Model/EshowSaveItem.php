<?php
/**
 * @category   Zanders
 * @package    Zanders_Eshow
 */

namespace Zanders\Eshow\Model;

use Zanders\Eshow\Model\ResourceModel\EshowSaveItem as EshowSaveResourceModel;
use Zanders\Eshow\Model\ResourceModel\EshowSaveItem\CollectionFactory as EshowSaveCollectionFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;

class EshowSaveItem extends AbstractModel
{
    protected $storeManager;
    protected $customerSession;
    protected $eshowsaveCollectionFactory;

    public function __construct(
        Context $context,
        Registry $registry,
        StoreManagerInterface $storeManager,
        Session $customerSession,
        EshowSaveCollectionFactory $eshowsaveCollectionFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->eshowsaveCollectionFactory =  $eshowsaveCollectionFactory;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
    }

    protected function _construct()
    {
        $this->_init(EshowSaveResourceModel::class);
    }
}
