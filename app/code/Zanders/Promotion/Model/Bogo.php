<?php
/**
 * @category   Zanders
 * @package    Zanders_Promotion
 */
namespace Zanders\Promotion\Model;

use Zanders\Promotion\Model\ResourceModel\Bogo as BogoResourceModel;
use Zanders\Promotion\Model\ResourceModel\Bogo\CollectionFactory as BogoCollectionFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;

class Bogo extends AbstractModel
{
    protected $storeManager;
    protected $customerSession;
    protected $bogoCollectionFactory;

    public function __construct(
        Context $context,
        Registry $registry,
        StoreManagerInterface $storeManager,
        Session $customerSession,
        BogoCollectionFactory $bogoCollectionFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->bogoCollectionFactory = $bogoCollectionFactory;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
    }

    protected function _construct()
    {
        $this->_init(BogoResourceModel::class);
    }
}
