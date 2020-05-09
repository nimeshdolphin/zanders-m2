<?php
namespace Zanders\Theme\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	protected $_customerSession;
	protected $stockState;

    public function __construct(
    	\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\CatalogInventory\Api\StockStateInterface $stockState
    )
    {
    	$this->scopeConfig = $scopeConfig;
		$this->_customerSession = $customerSession;
		$this->stockState = $stockState;
    }

    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getCustomerGroupId(){
        $customerGroup = '';
        if($this->_customerSession->isLoggedIn()){
            $customerGroup = $this->_customerSession->getCustomer()->getGroupId();
        }
        return $customerGroup;
    }

    public function getCustomerLogin(){
        if($this->_customerSession->isLoggedIn()){
            return true;
        }
        return false;
    }

    public function getStockQty($productId, $websiteId = null)
    {
        return $this->stockState->getStockQty($productId, $websiteId);
    }
}