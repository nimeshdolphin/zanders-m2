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

class Order extends \Magento\Framework\View\Element\Template
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

    protected $customer;
    protected $order;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        CustomerFactory $customerFactory,
        CustomerSession $customerSession,
        PromotionFactory $promotion,
        EshowFactory $eshow,
        ElliottHelper $elliottHelper,
        SportsHelper $sportsHelper,
        PromotionConfigHelper $promotionConfigHelper
    )
    {
        $this->customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
        $this->promotion = $promotion;
        $this->eshow = $eshow;
        $this->elliottHelper = $elliottHelper;
        $this->sportsHelper = $sportsHelper;
        $this->promotionConfigHelper = $promotionConfigHelper;
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

    public function getAllowFfl()
    {
        $customer = $this->getCustomer();
        return $customer->getAddFfls();
    }

    public function getTracking($order)
    {
        $tnumbers = array();
        foreach ($order->Notes as $note) {
            $content = array();
            $content[] = trim($note->NOTE_CONTENT_1);
            $content[] = trim($note->NOTE_CONTENT_2);
            $content[] = trim($note->NOTE_CONTENT_3);
            $content[] = trim($note->NOTE_CONTENT_4);
            $content[] = trim($note->NOTE_CONTENT_5);
            $content[] = trim($note->NOTE_CONTENT_6);
            $content[] = trim($note->NOTE_CONTENT_7);
            $content[] = trim($note->NOTE_CONTENT_8);
            $content[] = trim($note->NOTE_CONTENT_9);
            $content[] = trim($note->NOTE_CONTENT_10);

            $shipvia = null;
            $tnumber = null;
            $weight = null;
            $viacode = null;
            $url = null;

            foreach ($content as $line) {
                $info = explode(':', $line);
                if (trim($info[0]) == 'ShipVia') {
                    $via = explode(' ', trim($info[1]));
                    $shipvia = trim($info[1]);
                    $viacode = strtolower($via[0]);
                }
                if (trim($info[0]) == 'Track #') {
                    $tnumber = trim($info[1]);
                }
                if (trim($info[0]) == 'Weight') {
                    $weight = trim($info[1]);
                }
            }
            if ($viacode == 'ups') {
                $url = "http://wwwapps.ups.com/WebTracking/processInputRequest?HTMLVersion=5.0&loc=en_US&Requester=UPSHome&tracknum=" . $tnumber . "+&AgreeToTermsAndConditions=yes";
            }
            if ($viacode == 'fdx') {
                $url = "https://www.fedex.com/fedextrack/index.html?tracknumbers=" . $tnumber;
            }
            if ($viacode == 'fedex') {
                $url = "https://www.fedex.com/fedextrack/index.html?tracknumbers=" . $tnumber;
            }
            if ($viacode == 'usps') {
                $url = "http://trkcnfrm1.smi.usps.com/PTSInternetWeb/InterLabelInquiry.do?origTrackNum=" . $tnumber;
            }
            if ($trackinginfo['viacode'] == 'mail') {
                $url = "https://tools.usps.com/go/TrackConfirmAction!execute.action?formattedLabel=" . $trackinginfo['tnumber'];
            }
            if ($trackinginfo['viacode'] == 'spee-dee') {
                $url = "http://packages.speedeedelivery.com/packages.asp?tracking=" . $trackinginfo['tnumber'];
            }
            if ($trackinginfo['viacode'] == 'best') {
                $start = substr($trackinginfo['tnumber'], 0, 2);
                if ($start == '1Z') {
                    $url = "http://wwwapps.ups.com/WebTracking/processInputRequest?HTMLVersion=5.0&loc=en_US&Requester=UPSHome&tracknum=" . $trackinginfo['tnumber'] . "+&AgreeToTermsAndConditions=yes";
                }
                if ($start == '94') {
                    $url = "https://tools.usps.com/go/TrackConfirmAction!execute.action?formattedLabel=" . $trackinginfo['tnumber'];
                }
                if ($start == '01') {
                    $url = "https://www.fedex.com/fedextrack/index.html?tracknumbers=" . $trackinginfo['tnumber'];
                }
                if ($start == 'SP') {
                    $url = "http://packages.speedeedelivery.com/packages.asp?tracking=" . $trackinginfo['tnumber'];
                }
            }
            if (!is_null($shipvia) && !is_null($tnumber) && !is_null($viacode)) {
                $tracking = array('shipCompany' => $viacode, 'shipVia' => $shipvia, 'trackingNumber' => $tnumber, 'weight' => $weight, 'url' => $url);
                $tnumbers[] = $tracking;
            }
        }
        return $tnumbers;
    }

    public function getShippingMethod($code)
    {
        return $this->elliottHelper->getShippingMethod($code);
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
