<?php
namespace Zanders\Theme\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	protected $stockState;

    public function __construct(
    	\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
    	$this->scopeConfig = $scopeConfig;
    }

    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}