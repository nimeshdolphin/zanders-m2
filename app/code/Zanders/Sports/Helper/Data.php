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

    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        UrlInterface $urlManager
    )
    {
        $this->storeManager = $storeManager;
        $this->urlManager = $urlManager;
        parent::__construct($context);
    }

    public function getMediaUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }
}
