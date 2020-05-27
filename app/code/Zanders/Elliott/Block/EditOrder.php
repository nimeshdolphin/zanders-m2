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
use Zanders\Sports\Helper\Config as SportsConfigHelper;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Session as CustomerSession;

class EditOrder extends \Magento\Framework\View\Element\Template
{
    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var \Magento\Directory\Block\Data $directoryBlock
     */
    protected $directoryBlock;

    /**
     * @var \Magento\Customer\Helper\Address $addressHelper
     */
    protected $addressHelper;

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
     * @var SportsConfigHelper
     */
    protected $sportsConfigHelper;

    protected $customer;
    protected $order;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        CustomerFactory $customerFactory,
        CustomerSession $customerSession,
        \Magento\Directory\Block\Data $directoryBlock,
        \Magento\Customer\Helper\Address $addressHelper,
        PromotionFactory $promotion,
        EshowFactory $eshow,
        ElliottHelper $elliottHelper,
        SportsHelper $sportsHelper,
        SportsConfigHelper $sportsConfigHelper
    )
    {
        $this->customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
        $this->directoryBlock = $directoryBlock;
        $this->addressHelper = $addressHelper;
        $this->promotion = $promotion;
        $this->eshow = $eshow;
        $this->elliottHelper = $elliottHelper;
        $this->sportsHelper = $sportsHelper;
        $this->sportsConfigHelper = $sportsConfigHelper;
        $this->customer = null;
        $this->order = null;
        parent::__construct($context);
    }

    public function _prepareLayout()
    {
        $order = $this->getOrder();
        $this->pageConfig->getTitle()->set(__('Order #'). $order->NewDataSet->OrderHeader->ORDER_NO);
        return parent::_prepareLayout();
    }

    public function getCustomer()
    {
        if (is_null($this->customer)) {
            $this->customer = $this->customerFactory->create()->load($this->sportsHelper->getCustomerId());
        }
        return $this->customer;
    }

    public function getOrder()
    {
        if (is_null($this->order)) {
            $customer = $this->getCustomer();
            $ordInq = new \Zanders_Elliott_Model_Elliott_Orderinquiry();
            $order = $ordInq->GetOneOrderDetail(array('orderNo' => $this->getRequest()->getParam('number')));
            if ($order->GetOneOrderDetailResult->ReturnCode == 0) {
                $ordxml = simplexml_load_string($order->GetOneOrderDetailResult->OrderDetail->any);
                if ($ordxml->NewDataSet->OrderHeader->ORDER_CUSTOMER_NO == $customer->getNumber()) {
                    $this->order = $ordxml;
                }
            }
        }
        return $this->order;
    }

    public function getAttributeValidationClass($attributeCode)
    {
        return $this->addressHelper->getAttributeValidationClass($attributeCode);
    }

    public function getCountryHtmlSelect()
    {
        return $this->directoryBlock->getCountryHtmlSelect();

        /*$countryId = Mage::helper('core')->getDefaultCountry();
        $select = $this->getLayout()->createBlock('core/html_select')
            ->setName('shipping[country_id]')
            ->setId('shipping:country_id')
            ->setTitle(Mage::helper('checkout')->__('Country'))
            ->setClass('validate-select')
            ->setValue($countryId)
            ->setOptions($this->getCountryOptions());

        return $select->getHtml();*/
    }

    public function getSecureUrl($url, $params = array())
    {
        return $this->sportsHelper->getSecureUrl($url, $params = array());
    }

    public function getShippingMethod($code)
    {
        return $this->elliottHelper->getShippingMethod($code);
    }

    public function getConfig($configPath)
    {
        return $this->sportsConfigHelper->getConfig($configPath);
    }

    public function getTerms($code)
    {
        $elliottCustomer = new \Zanders_Elliott_Model_Elliott_Customer();
        $terms = $elliottCustomer->getTermsList();
        return $terms[$code];
    }

    public function formatPrice($amount)
    {
        return $this->elliottHelper->formatPrice($amount);
    }

    public function getTrackingFromNotes($notes)
    {
        return $this->elliottHelper->getTrackingFromNotes($notes);
    }

    public function splitInjection($str, $length = 50, $needle = '-', $insert = ' ')
    {
        return $this->elliottHelper->splitInjection($str, $length, $needle, $insert);
    }
}
