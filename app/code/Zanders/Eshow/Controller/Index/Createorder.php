<?php
declare(strict_types=1);

namespace Zanders\Eshow\Controller\Index;

use \Magento\Framework\Controller\ResultFactory;
use Zanders\Eshow\Model\EshowOrderFactory as EshowOrderFactory;
use Zanders\Eshow\Model\EshowFactory;
use Zanders\Eshow\Model\ResourceModel\EshowSaveItem\CollectionFactory as EshowSaveCollectionFactory;
use Zanders\Eshow\Model\EshowSaveItem;

class Createorder extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;
    protected $_helper;
    protected $_storeconfig;
    protected $_messageManager;
    protected $_eshowOrderFacoty;
    protected $_eshowFactory;
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
        \Magento\Framework\Message\ManagerInterface $messageManager,
        EshowOrderFactory $eshowOrderFactoty,
        EshowSaveCollectionFactory $eshowsaveCollectionFactory,
        EshowFactory $eshowFactory,
        EshowSaveItem $eshowSaveItem
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_helper =  $_helper;
        $this->_storeconfig = $storeconifg;
        $this->_messageManager = $messageManager;
        $this->_eshowOrderFacoty = $eshowOrderFactoty;
        $this->_eshowFactory = $eshowFactory;
        $this->eshowsaveCollectionFactory =  $eshowsaveCollectionFactory;
        $this->eshowSaveItem = $eshowSaveItem;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        if ($this->_helper->isLoggedIn() && $this->_storeconfig->getValue('sports/eshows/enable')) {
            $resultPage = $this->resultPageFactory->create();

            $eshoworder = $this->getRequest()->getParams();
            $customer = $this->_helper->getCustomerId();
            //$this->_eshowOrderFacoty->create()->load();
            $eshow = $this->_eshowFactory->create()->load($eshoworder['eshow']);
            $customer = $this->_helper->getCustomerData();
            $emailto = explode("\n", $this->_storeconfig->getValue('sports/eshows/send_to'));



            $eshowsaveorder = $this->_eshowOrderFacoty->create();
            $eshowsaveorder->setShowId($eshoworder['eshow'])->setCustomerNumber($customer->getNumber())->setCustomerName($customer->getName())->save();

            $emailTemplate = $resultPage->getLayout()->createBlock('Zanders\Eshow\Block\Eshowemail', 'eShow Order', ['data' => ['eshoworder' => $eshoworder, 'eshow' => $eshow, 'customer' => $customer, 'ordernumber' => $eshowsaveorder->getId()]])->toHtml();
            
            $email = new \Zend_Mail();
            $email->setSubject("Stocking Dealer Order from " . $customer->getNumber() . " for Promotion id: " . $eshoworder['eshow']);
            $email->setBodyHtml($emailTemplate);
            $email->setFrom($customer->getEmail(), $customer->getName());
            $email->addTo($customer->getEmail(), $customer->getName());
            foreach ($emailto as $sendto) {
                //$email->addBcc($sendto);
            }
            $email->send();



            $eshowsaveorder->setSave($emailTemplate)->save();

            $savedinfo = $this->eshowsaveCollectionFactory->create();
            $savedinfo->addFieldToFilter('show_id', $eshoworder['eshow'])->addFieldToFilter('customer_id', $customer->getId());
            if ($savedinfo->count() > 0) {
                $this->eshowSaveItem->load($savedinfo->getFirstItem()->getId())->delete();
            }
                
            $this->_messageManager->addSuccessMessage('Your Order has been created. Your Order Number is ' . $eshowsaveorder->getId() . '.');
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }
    }
}
