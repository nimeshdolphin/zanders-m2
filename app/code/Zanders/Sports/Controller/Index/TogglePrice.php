<?php
/**
 * @category   Zanders
 * @package    Zanders_Sports
 */

namespace Zanders\Sports\Controller\Index;

use Magento\Framework\Controller\ResultFactory;

class TogglePrice extends \Magento\Framework\App\Action\Action
{
    protected $customerSession;
    protected $customerFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        array $data = []
    )
    {
        $this->customerSession = $customerSession;
        $this->customerFactory = $customerFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if ($this->customerSession->isLoggedIn()) {
            $customer = $this->customerFactory->create();
            $customer->load($this->customerSession->getCustomer()->getId());
            $customer->setHidePrice((int)!$customer->getHidePrice());
            $customer->save();
        }
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }
}
