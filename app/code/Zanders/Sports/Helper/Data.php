<?php
/**
 * @category   Zanders
 * @package    Zanders_Sports
 */

namespace Zanders\Sports\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\UrlInterface;

class Data extends AbstractHelper
{
    /*
    * @var StoreManager
    */
    protected $storeManager;

    /*
    * @var UrlInterface
    */
    protected $urlManager;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    private $httpContext;

    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        UrlInterface $urlManager,
        \Magento\Framework\App\Http\Context $httpContext
    )
    {
        $this->storeManager = $storeManager;
        $this->urlManager = $urlManager;
        $this->httpContext = $httpContext;
        parent::__construct($context);
    }

    public function isLoggedIn()
    {
        $isLoggedIn = $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
        return $isLoggedIn;
    }

    public function getMediaUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }
}
