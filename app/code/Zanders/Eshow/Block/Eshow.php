<?php
/**
 * @category   Zanders
 * @package    Zanders_Eshow
 */

namespace Zanders\Eshow\Block;

use Zanders\Promotion\Model\PromotionFactory;
use Zanders\Eshow\Model\EshowFactory;
use Zanders\Sports\Helper\Data as SportsHelper;
use Zanders\Promotion\Helper\Config as PromotionConfigHelper;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Eshow extends \Magento\Framework\View\Element\Template
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

    /**
     * @var EshowFactory
     */
    protected $loadedEshow;

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
        $this->loadedEshow = null;
        parent::__construct($context);
    }

    public function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set($this->getEshow()->getTitle());
        return parent::_prepareLayout();
    }

    public function getEshow()
    {
        if(is_null($this->loadedEshow)){
            $eshowId = $this->getRequest()->getParam('id');
            $this->loadedEshow = $this->eshow->create()->load($eshowId);
        }
        return $this->loadedEshow;
    }

    public function getConfig($configPath)
    {
        return $this->promotionConfigHelper->getConfig($configPath);
    }

    public function getMediaUrl()
    {
        return $this->sportsHelper->getMediaUrl();
    }

    public function getEshowPromotion()
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
}
