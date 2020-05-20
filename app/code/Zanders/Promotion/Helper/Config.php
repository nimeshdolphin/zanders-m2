<?php
/**
 * @category   Zanders
 * @package    Zanders_Promotion
 */
namespace Zanders\Promotion\Helper;

use Magento\Customer\Model\ResourceModel\Group\Collection;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Config extends AbstractHelper
{
    const XML_PATH_ENABLED = 'sports/promotions/currentcatalog';

    public function isEnabled()
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_ENABLED);
    }

    public function getConfig($configPath)
    {
        return $this->scopeConfig->getValue($configPath);
    }
}
