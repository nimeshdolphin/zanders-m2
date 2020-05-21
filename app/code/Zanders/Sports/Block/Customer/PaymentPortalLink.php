<?php
/**
 * @category   Zanders
 * @package    Zanders_Sports
 */

namespace Zanders\Sports\Block\Customer;

class PaymentPortalLink extends \Magento\Framework\View\Element\Html\Link\Current
{
    protected $customerSession;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    )
    {
        $this->customerSession = $customerSession;
        parent::__construct($context, $defaultPath, $data);
    }

    protected function _toHtml()
    {
        $responseHtml = null;
        if ($this->customerSession->isLoggedIn() && $this->customerSession->getCustomer()->getPaymentPortal())
            $responseHtml = parent::_toHtml(); //Return link html
        return $responseHtml;
    }
}
