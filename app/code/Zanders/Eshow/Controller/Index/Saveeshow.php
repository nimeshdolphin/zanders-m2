<?php
declare(strict_types=1);

namespace Zanders\Eshow\Controller\Index;

use Zanders\Eshow\Model\ResourceModel\EshowSaveItem\CollectionFactory as EshowSaveCollectionFactory;
use Zanders\Eshow\Model\EshowSaveItemFactory;
use \Magento\Framework\Controller\ResultFactory;

class Saveeshow extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;
    protected $_helper;
    protected $_storeconfig;
    protected $_messageManager;


    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Zanders\Sports\Helper\Data $_helper,
        \Magento\Framework\App\Config\ScopeConfigInterface $storeconifg,
        EshowSaveCollectionFactory $eshowsaveCollectionFactory,
        EshowSaveItemFactory $eshowSaveItemFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_helper =  $_helper;
        $this->_storeconfig = $storeconifg;
        $this->eshowsaveCollectionFactory =  $eshowsaveCollectionFactory;
        $this->eshowSaveItemFactory =  $eshowSaveItemFactory;
        $this->_messageManager = $messageManager;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        //echo "<pre>"; print_r($this->getRequest()->getParams()); exit;
        if ($this->_helper->isLoggedIn() && $this->_storeconfig->getValue('sports/eshows/enable')) {

            $eshoworder = $this->getRequest()->getParams();
            $customer = $this->_helper->getCustomerId();
            $this->eshowsaveCollectionFactory = $this->eshowsaveCollectionFactory->create();
            $this->eshowsaveCollectionFactory->addFieldToFilter('show_id', $eshoworder['eshow'])->addFieldToFilter('customer_id', $customer);
            //echo $this->eshowsaveCollectionFactory->count(); exit;
            $savedinfo = $this->eshowSaveItemFactory->create();
            if ($this->eshowsaveCollectionFactory->count() > 0) {
                $savedinfo->load($this->eshowsaveCollectionFactory->getFirstItem()->getId());
            } else {
                $savedinfo->setShowId($eshoworder['eshow'])->setCustomerId($customer);
            }
            $savedinfo->setSelections(serialize($eshoworder))->save();
            $this->_messageManager->addSuccessMessage('Your Special Order has Been Saved for later!');
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }

        //return $this->resultPageFactory->create();
    }
}
