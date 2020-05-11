<?php
namespace Zanders\Theme\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	protected $_customerSession;
	protected $stockState;
    protected $customer;

    public function __construct(
    	\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\CatalogInventory\Api\StockStateInterface $stockState,
        \Magento\Customer\Block\Account\Customer $customer
    )
    {
    	$this->scopeConfig = $scopeConfig;
		$this->_customerSession = $customerSession;
		$this->stockState = $stockState;
        $this->customer = $customer;
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
        if($this->customer->customerLoggedIn()){
            $customerGroup = $this->_customerSession->getCustomer()->getGroupId();
        }
        return $customerGroup;
    }

    public function getCustomerLogin(){
        if($this->customer->customerLoggedIn()){
            return true;
        }
        return false;
    }

    public function getStockQty($productId, $websiteId = null)
    {
        return $this->stockState->getStockQty($productId, $websiteId);
    }
}