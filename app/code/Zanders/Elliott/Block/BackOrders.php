<?php
/**
 * @category   Zanders
 * @package    Zanders_Elliott
 */

namespace Zanders\Elliott\Block;

use Zanders\Promotion\Model\PromotionFactory;
use Zanders\Eshow\Model\EshowFactory;
use Zanders\Elliott\Helper\Data as ElliottHelper;
use Zanders\Sports\Helper\Data as SportsHelper;
use Zanders\Promotion\Helper\Config as PromotionConfigHelper;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Session\SessionManagerInterface as CoreSession;

class BackOrders extends \Magento\Framework\View\Element\Template
{
    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var CoreSession
     */
    protected $coreSession;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

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
     * @var ElliottHelper
     */
    protected $elliottHelper;

    /**
     * @var PromotionConfigHelper
     */
    protected $promotionConfigHelper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        CustomerFactory $customerFactory,
        CustomerSession $customerSession,
        CoreSession $coreSession,
        PromotionFactory $promotion,
        EshowFactory $eshow,
        ElliottHelper $elliottHelper,
        SportsHelper $sportsHelper,
        PromotionConfigHelper $promotionConfigHelper
    )
    {
        $this->customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
        $this->coreSession = $coreSession;
        $this->promotion = $promotion;
        $this->eshow = $eshow;
        $this->elliottHelper = $elliottHelper;
        $this->sportsHelper = $sportsHelper;
        $this->promotionConfigHelper = $promotionConfigHelper;

        parent::__construct($context);
    }

    public function _prepareLayout()
    {
        $this->setSortDir();
        $this->pageConfig->getTitle()->set(__('My Back Order List'));
        return parent::_prepareLayout();
    }

    public function setSortDir()
    {
        $this->coreSession->setOrderlistsort(
            $this->getRequest()->getParam('sortby') ? $this->getRequest()->getParam('sortby') : 'ORDER_DATE'
        );
        $this->coreSession->setOrderlistsortdir(
            $this->getRequest()->getParam('sortdir') ? $this->getRequest()->getParam('sortdir') : 'DESC'
        );
    }

    public function getConfig($configPath)
    {
        return $this->promotionConfigHelper->getConfig($configPath);
    }

    public function getOrderSortBy()
    {
        return $this->coreSession->getOrderlistsort();
    }

    public function getOrderSortDir()
    {
        return $this->coreSession->getOrderlistsortdir();
    }

    public function getBackorderList()
    {
        $customer = $this->customerFactory->create()->load($this->sportsHelper->getCustomerId());
        $bolist = new \Zanders_Elliott_Model_Elliott_Query();
        $items = $bolist->getCustomerBackorderList($customer->getNumber(), $this->getOrderSortBy(), $this->getOrderSortDir());
        return $items;
    }

    public function formatPrice($amount)
    {
        return $this->elliottHelper->formatPrice($amount);
    }
}