<?php
/**
 * @category   Zanders
 * @package    Zanders_Promotion
 */

namespace Zanders\Promotion\Block;

use Zanders\Promotion\Model\PromotionFactory;
use Zanders\Eshow\Model\EshowFactory;
use Zanders\Sports\Helper\Data as SportsHelper;
use Zanders\Promotion\Helper\Config as PromotionConfigHelper;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Promotional extends \Magento\Framework\View\Element\Template
{
    /**
     * @var PromotionFactory
     */
    protected $promotion;

    /**
     * @var EshowFactory
     */
    protected $eshow;

    /**
     * @var SportsHelper
     */
    protected $sportsHelper;

    /**
     * @var PromotionConfigHelper
     */
    protected $promotionConfigHelper;

    /**
     * @var DateTime
     */
    protected $dateTime;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        PromotionFactory $promotion,
        EshowFactory $eshow,
        SportsHelper $sportsHelper,
        PromotionConfigHelper $promotionConfigHelper,
        DateTime $dateTime
    )
    {
        $this->promotion = $promotion;
        $this->eshow = $eshow;
        $this->sportsHelper = $sportsHelper;
        $this->promotionConfigHelper = $promotionConfigHelper;
        $this->dateTime = $dateTime;
        parent::__construct($context);
    }

    public function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Promotions & Flyers'));
        return parent::_prepareLayout();
    }

    public function getConfig($configPath)
    {
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
            ->setOrder('title', 'ASC');
        return $collection;
    }

    public function getDealerPromotions()
    {
        $promotion = $this->promotion->create();
        $collection = $promotion->getCollection();
        $collection->addFieldToFilter('type', \Zanders\Promotion\Model\Config\Source\Type::TYPE_DEALER)
            ->setOrder('title', 'ASC');
        return $collection;
    }

    public function getEshowPromotions()
    {
        $eshow = $this->eshow->create();
        $currentDate = $this->dateTime->date('Y-m-d');
        $collection = $eshow->getCollection();
        $collection->addFieldToFilter('stocking_dealer', 1)
            ->addFieldToFilter('start_date', array('lteq' => $currentDate))
            ->addFieldToFilter('end_date', array('gteq' => $currentDate))
            ->setOrder('title', 'ASC');
        return $collection;
    }

    public function getConsumerPromotions()
    {
        $promotion = $this->promotion->create();
        $collection = $promotion->getCollection();
        $collection->addFieldToFilter('type', \Zanders\Promotion\Model\Config\Source\Type::TYPE_CONSUMER)
            ->setOrder('title', 'ASC');
        return $collection;
    }
}
