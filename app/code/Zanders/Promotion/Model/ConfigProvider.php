<?php
/**
 * @category   Zanders
 * @package    Zanders_Promotion
 */

namespace Zanders\Promotion\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface as UrlBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Zanders\Promotion\Helper\Data as PromotionHelper;
use Magento\Checkout\Model\Session as CheckoutSession;

class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var UrlBuilder
     */
    protected $urlBuilder;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var PromotionHelper
     */
    protected $promotionHelper;

    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        UrlBuilder $urlBuilder,
        CheckoutSession $checkoutSession,
        PromotionHelper $promotionHelper
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->urlBuilder = $urlBuilder;
        $this->checkoutSession = $checkoutSession;
        $this->promotionHelper = $promotionHelper;
    }

    public function getConfig()
    {
        $quoteData = $this->checkoutSession->getQuote();
        $appliedCharges = $quoteData->getPromotionDescription();
        return [
            'promotion' => [
                'is_enabled' => $this->promotionHelper->isEnabled(),
                'url_save_extra_charge' => $this->urlBuilder->getUrl('promotion/checkout/saveextracharge'),
                'title' => $this->promotionHelper->getExtraChargeTitle(),
                'applied_charges' => $appliedCharges
            ]
        ];
    }
}
