<?php
/**
 * @category   Zanders
 * @package    Zanders_Sports
 */
namespace Zanders\Sports\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Config extends AbstractHelper
{
    public function getConfig($configPath)
    {
        return $this->scopeConfig->getValue($configPath);
    }
}
