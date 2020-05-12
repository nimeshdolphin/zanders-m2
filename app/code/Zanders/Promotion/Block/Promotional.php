<?php
/**
 * @category   Zanders
 * @package    Zanders_Promotion
 */
namespace Zanders\Promotion\Block;

use Zanders\Promotion\Model\PromotionFactory;
use Zanders\Sports\Helper\Data as SportsHelper;
use Zanders\Promotion\Helper\Config as PromotionConfigHelper;

class Promotional extends \Magento\Framework\View\Element\Template
{
    /**
     * @var PromotionFactory
     */
    protected $promotion;

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
        PromotionFactory $promotion,
        PromotionConfigHelper $promotionConfigHelper,
        SportsHelper $sportsHelper
    ) {
        $this->promotion = $promotion;
        $this->promotionConfigHelper = $promotionConfigHelper;
        $this->sportsHelper = $sportsHelper;
        parent::__construct($context);
    }

    public function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Promotions & Flyers'));
        return parent::_prepareLayout();
    }

    public function getConfig($configPath){
        return $this->promotionConfigHelper->getConfig($configPath);
    }

    public function getMediaUrl()
    {
        return $this->sportsHelper->getMediaUrl();
    }

    public function getGeneralPromotions()
    {
        $promotion = $this->promotion->create();
        $collection = $promotion->getCollection();
        $collection->addFieldToFilter('type', \Zanders\Promotion\Model\Config\Source\Type::TYPE_GENERAL)
            ->setOrder('title','ASC');
        return $collection;
    }

    public function getDealerPromotions()
    {
        $promotion = $this->promotion->create();
        $collection = $promotion->getCollection();
        $collection->addFieldToFilter('type', \Zanders\Promotion\Model\Config\Source\Type::TYPE_DEALER)
            ->setOrder('title','ASC');
        return $collection;
    }

    public function getConsumerPromotions()
    {
        $promotion = $this->promotion->create();
        $collection = $promotion->getCollection();
        $collection->addFieldToFilter('type', \Zanders\Promotion\Model\Config\Source\Type::TYPE_CONSUMER)
            ->setOrder('title','ASC');
        return $collection;
    }
}
