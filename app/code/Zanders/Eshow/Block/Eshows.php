<?php
/**
 * @category   Zanders
 * @package    Zanders_Eshow
 */

namespace Zanders\Eshow\Block;

use Zanders\Eshow\Model\EshowFactory;
use Zanders\Sports\Helper\Data as SportsHelper;
use Zanders\Promotion\Helper\Config as PromotionConfigHelper;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Eshows extends \Magento\Framework\View\Element\Template
{
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
        EshowFactory $eshow,
        SportsHelper $sportsHelper,
        PromotionConfigHelper $promotionConfigHelper,
        DateTime $dateTime
    ) {
        $this->eshow = $eshow;
        $this->sportsHelper = $sportsHelper;
        $this->promotionConfigHelper = $promotionConfigHelper;
        $this->dateTime = $dateTime;
        parent::__construct($context);
    }

    public function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Dealer Promotions'));
        return parent::_prepareLayout();
    }

    public function getConfig($configPath)
    {
        return $this->promotionConfigHelper->getConfig($configPath);
    }

    public function getEshowPromotions()
    {
        $eshow = $this->eshow->create();
        $currentDate = $this->dateTime->date('Y-m-d');
        $collection = $eshow->getCollection();
        $collection
            ->addFieldToFilter('start_date', ['lteq' => $currentDate])
            ->addFieldToFilter('end_date', ['gteq' => $currentDate])
            ->setOrder('title', 'ASC');
        return $collection;
    }
}
