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
    * @var _dir
     */
    protected $_dir;

    /*
    * @var StoreManager
    */
    protected $storeManager;

    /*
    * @var UrlInterface
    */
    protected $urlManager;

    /*
    * @var StockState
    */
    protected $stockState;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    private $httpContext;

    /*
    * @var CustomerSession
    */
    protected $customerSession;

    /*
    * @var CustomerFactory
    */
    protected $customerFactory;

    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        UrlInterface $urlManager,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\CatalogInventory\Api\StockStateInterface $stockState,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Filesystem\DirectoryList $dir
    )
    {
        $this->storeManager = $storeManager;
        $this->urlManager = $urlManager;
        $this->httpContext = $httpContext;
        $this->stockState = $stockState;
        $this->customerSession = $customerSession;
        $this->customerFactory = $customerFactory;
        $this->_dir = $dir;
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

    public function getCustomerGroupId()
    {
        $customerGroupId = '';
        if($this->isLoggedIn()){
            $customerGroupId = $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP);
            $customerGroupId = 3;
        }
        return $customerGroupId;
    }

    public function getHidePrice()
    {
        return $this->httpContext->getValue(\Zanders\CustomerSession\Model\Customer\Context::CONTEXT_CUSTOMER_HIDEPRICE);
    }

    public function getStockQty($productId, $websiteId = null)
    {
        return $this->stockState->getStockQty($productId, $websiteId);
    }

    public function getCustomerId(){
        return $this->httpContext->getValue(\Zanders\CustomerSession\Model\Customer\Context::CONTEXT_CUSTOMER_ID);
    }

    public function getRelativePath($path)
    {
        return $this->_dir->getPath($path);
    }
}
