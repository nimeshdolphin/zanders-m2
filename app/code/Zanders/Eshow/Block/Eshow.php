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
use Zanders\Eshow\Model\EshowSaveItemFactory;
use Zanders\Eshow\Model\ResourceModel\EshowSaveItem\CollectionFactory as EshowSaveCollectionFactory;

class Eshow extends \Magento\Framework\View\Element\Template
{

    /**
     * @var _productRepository
     */
    protected $_productRepository;

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
    protected $_savedShow = null;
    protected $eshowsaveCollectionFactory;
    protected $_prevShow = null;
    protected $_nextShow = null;
    protected $_currentShowList = null;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        PromotionFactory $promotion,
        EshowFactory $eshow,
        SportsHelper $sportsHelper,
        PromotionConfigHelper $promotionConfigHelper,
        DateTime $dateTime,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        EshowSaveCollectionFactory $_eshowsaveCollectionFactory
    ) {
        $this->promotion = $promotion;
        $this->eshow = $eshow;
        $this->sportsHelper = $sportsHelper;
        $this->promotionConfigHelper = $promotionConfigHelper;
        $this->dateTime = $dateTime;
        $this->loadedEshow = null;
        $this->_productRepository = $productRepository;
        $this->eshowsaveCollectionFactory =  $_eshowsaveCollectionFactory;
        parent::__construct($context);
    }

    public function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set($this->getEshow()->getTitle());
        return parent::_prepareLayout();
    }

    public function getEshow()
    {
        if (is_null($this->loadedEshow)) {
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
            ->addFieldToFilter('start_date', ['lteq' => $currentDate])
            ->addFieldToFilter('end_date', ['gteq' => $currentDate])
            ->setOrder('title', 'ASC');
        return $collection;
    }

    public function getProductById($id)
    {
        return $this->_productRepository->getById($id);
    }

    public function getSavedEshow()
    {
        //return false;
        $eshowId = $this->getRequest()->getParam('id');
        $customer = $this->sportsHelper->getCustomerId();
        $savedinfo = $this->eshowsaveCollectionFactory->create();
                    $savedinfo->addFieldToFilter('show_id', $eshowId)->addFieldToFilter('customer_id', $customer);
        if ($savedinfo->count() > 0) {
            $savedinfo = $savedinfo->getFirstItem();
            $this->_savedShow = unserialize($savedinfo->getSelections());
        }
        
        return $this->_savedShow;
    }

    public function getPrevEshow()
    {
        if (is_null($this->_prevShow)) {
            $showlist = $this->getEshowPromotion();
            foreach ($showlist as $eshow) {
                $this->_currentShowList[] = $eshow->getId();
            }
            $eshowid = array_search($this->getEshow()->getId(), $this->_currentShowList) - 1;
            if ($eshowid<0) {
                $eshowid = $showlist->count() - 1;
            }
            $this->_prevShow = $this->_currentShowList[$eshowid];
        }
        return $this->_prevShow;
    }

    public function getNextEshow()
    {
        if (is_null($this->_nextShow)) {
            $showlist = $this->getEshowPromotion();
            foreach ($showlist as $eshow) {
                $this->_currentShowList[] = $eshow->getId();
            }
            $eshowid = array_search($this->getEshow()->getId(), $this->_currentShowList) + 1;
            if ($eshowid >= $showlist->count()) {
                $eshowid = 0;
            }
            $this->_nextShow = $this->_currentShowList[$eshowid];
        }
        return $this->_nextShow;
    }
}
