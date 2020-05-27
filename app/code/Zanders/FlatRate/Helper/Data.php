<?php
/**
 * @category   Zanders
 * @package    Zanders_FlatRate
 */

namespace Zanders\FlatRate\Helper;

use Magento\Customer\Model\ResourceModel\Customer as CustomerResource;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Data extends AbstractHelper
{
    /**
     * @var CustomerResource
     */
    protected $customerResource;

    const XML_PATH_EXPEDITEDFLATRATE = 'sports/zandersdefaults/expeditedflatrate';

    public function __construct(Context $context, CustomerResource $customerResource)
    {
        $this->customerResource = $customerResource;
        parent::__construct($context);
    }

    public function getFlatRateExtras($customerTier = null)
    {
        $flatRateExtras = explode("\n", $this->scopeConfig->getValue(self::XML_PATH_EXPEDITEDFLATRATE));
        $shippingTier = 1;

        if (!is_null($customerTier)) {
            $shippingTier = $this->customerResource
                ->getAttribute('shipping_tier')
                ->getSource()
                ->getOptionText($customerTier);
        }

        $frExtras = array();
        foreach ($flatRateExtras as $rate) {
            $info = explode(',', trim($rate));
            $frExtras[$info[0]] = $info[intval($shippingTier)];
        }
        return $frExtras;
    }
}
