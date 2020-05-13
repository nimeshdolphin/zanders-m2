<?php
/**
 * @category   Zanders
 * @package    Zanders_Sports
 */

namespace Zanders\Sports\Block;

use Zanders\Sports\Helper\Data as SportsHelper;
use Zanders\Promotion\Helper\Config as PromotionConfigHelper;

class Message extends \Magento\Framework\View\Element\Template
{
    const ZANDERS_SPORTS_ZALERT_ACTIVE = 'sports/zalert/active';
    const ZANDERS_SPORTS_ZALERT_MESSAGE = 'sports/zalert/message';

    /**
     * @var SportsHelper
     */
    protected $sportsHelper;

    /**
     * @var PromotionConfigHelper
     */
    protected $promotionConfigHelper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        PromotionConfigHelper $promotionConfigHelper,
        SportsHelper $sportsHelper
    )
    {
        $this->promotionConfigHelper = $promotionConfigHelper;
        $this->sportsHelper = $sportsHelper;
        parent::__construct($context);
    }

    public function getConfig($configPath)
    {
        return $this->promotionConfigHelper->getConfig($configPath);
    }

    public function showMessage()
    {
        return $this->sportsHelper->isLoggedIn() && $this->isEnable();
    }

    public function isEnable()
    {
        return $this->getConfig(self::ZANDERS_SPORTS_ZALERT_ACTIVE);
    }

    public function getMessage()
    {
        return $this->getConfig(self::ZANDERS_SPORTS_ZALERT_MESSAGE);
    }
}
