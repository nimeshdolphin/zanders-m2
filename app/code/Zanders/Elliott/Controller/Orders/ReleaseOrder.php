<?php
/**
 * @category   Zanders
 * @package    Zanders_Elliott
 */

namespace Zanders\Elliott\Controller\Orders;

use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;

class ReleaseOrder extends \Zanders\Elliott\Controller\AbstractAccount implements HttpGetActionInterface
{
    protected $_notallowed = array('C','A','F');

    protected $customerSession;
    protected $customerFactory;
    protected $urlInterface;
    protected $dateTime;
    protected $formKey;
    protected $sportsHelper;
    protected $sportsConfigHelper;
    protected $elliottHelper;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Zanders\Sports\Helper\Data $sportsHelper,
        \Zanders\Sports\Helper\Config $sportsConfigHelper,
        \Zanders\Elliott\Helper\Data $elliottHelper,
        array $data = []
    )
    {
        $this->customerSession = $customerSession;
        $this->customerFactory = $customerFactory;
        $this->urlInterface = $urlInterface;
        $this->dateTime = $dateTime;
        $this->sportsHelper = $sportsHelper;
        $this->sportsConfigHelper = $sportsConfigHelper;
        $this->elliottHelper = $elliottHelper;
        $this->messageManager = $context->getMessageManager();
        parent::__construct($context);
    }

    public function execute()
    {
        $customer = $this->customerFactory->create();
        $customer->load($this->customerSession->getCustomer()->getId());

        $elliottOrderInquiry = new \Zanders_Elliott_Model_Elliott_Orderinquiry();
        $order = $elliottOrderInquiry->GetOneOrderDetail(array('orderNo' => $this->getRequest()->getParam('number')));

        if ($order->GetOneOrderDetailResult->ReturnCode == 0) {
            $ordxml = simplexml_load_string($order->GetOneOrderDetailResult->OrderDetail->any);
            if ($ordxml->NewDataSet->OrderHeader->ORDER_CUSTOMER_NO == $customer->getNumber()) {
                if (!in_array($ordxml->NewDataSet->OrderHeader->ORDER_RELEASE_FLAG, $this->_notallowed)) {

                    $orderheader = array();
                    $orderheader['ORDER_NO'] = $this->getRequest()->getParam('number');

                    $orderheader['ORDER_FRGHT_PAY_CODE'] = " ";
                    $orderheader['ORDER_DATE'] = '0001-01-01';
                    $orderheader['ORDER_CHECK_DATE'] = '0001-01-01';
                    $orderheader['ORDER_DATE_PICKED'] = '0001-01-01';
                    $orderheader['ORDER_DATE_BILLED'] = '0001-01-01';
                    $orderheader['ORDER_INVOICE_DATE'] = '0001-01-01';
                    $orderheader['ORDER_POSTED_DATE'] = '0001-01-01';
                    $orderheader['ORDER_SHIP_ACK_DATE'] = '0001-01-01';
                    $orderheader['ORDER_PICK_TICK_CUT'] = '0001-01-01';
                    $orderheader['ORDER_DATE_ENTERED'] = '0001-01-01';
                    $orderheader['ORDER_SHIPPING_DATE'] = $this->dateTime->date('Y-m-d');
                    $orderheader['OrderTimeReleaseNew'] = 0;
                    $orderheader['ORD_FREIGHT_AMOUNT'] = 0;
                    $orderheader['ORDER_MISC_CHRG_AMT'] = 0;
                    $orderheader['ORDER_SLS_PCT_COMM_1'] = 0;
                    $orderheader['ORDER_SLS_PCT_COMM_2'] = 0;
                    $orderheader['ORDER_SLS_PCT_COMM_3'] = 0;
                    //$orderheader['ORDER_CHECK_NO']= 0;
                    $orderheader['ORDER_PAYMENT_AMOUNT'] = 0;
                    $orderheader['ORDER_TAX_PERCENT_1'] = 0;
                    $orderheader['ORDER_TAX_PERCENT_2'] = 0;
                    $orderheader['ORDER_TAX_PERCENT_3'] = 0;
                    //$orderheader['ORDER_APPLY_TO_NO']= 0;
                    //$orderheader['ORDER_NO_ALT']= 0;
                    $orderheader['ORDER_SLS_COMM_AMT_1'] = 0;
                    $orderheader['ORDER_SLS_COMM_AMT_2'] = 0;
                    $orderheader['ORDER_SLS_COMM_AMT_3'] = 0;
                    $orderheader['ORDER_TOTAL_SALE_AMT'] = 0;
                    $orderheader['ORDER_TOTL_TAXBL_AMT'] = 0;
                    $orderheader['ORDER_TOTAL_COST'] = 0;
                    $orderheader['ORDER_TOTAL_WEIGHT'] = 0;
                    $orderheader['ORDER_COMM_PERCENT'] = 0;
                    $orderheader['ORDER_COMM_AMOUNT'] = 0;
                    $orderheader['ORDER_PAYMNT_DIS_AMT'] = 0;
                    $orderheader['ORDER_DISC_PERCENT'] = 0;
                    //$orderheader['ORDER_INVOICE_NO']= 0;
                    $orderheader['ORDER_ACC_MISC_AMT'] = 0;
                    $orderheader['ORDER_ACC_TOT_TAXBLE'] = 0;
                    $orderheader['ORDER_ACC_FREIGHT'] = 0;
                    $orderheader['ORDER_ACC_SALES_TAX'] = 0;
                    $orderheader['ORDER_ACC_TOT_SALES'] = 0;
                    $orderheader['ORD_SALES_TAX_AMT_1'] = 0;
                    $orderheader['ORD_SALES_TAX_AMT_2'] = 0;
                    $orderheader['ORD_SALES_TAX_AMT_3'] = 0;

                    $elliottOrder = new \Zanders_Elliott_Model_Elliott_Order();
                    $response = $elliottOrder->ChangeHeader(array('ChangeHeaderInput' => $orderheader));

                    if ($response->ChangeHeaderResult->ReturnCode == 0) {
                        $this->messageManager->addSuccessMessage(__('This order was released.'));
                    } else {
                        $this->messageManager->addErrorMessage(__('There was a problem releasing this order. Please Call to release.'));
                    }
                } else {
                    $this->messageManager->addErrorMessage(__('This order could not be released. Please Call to release.'));
                }
            }
        }
        $this->_redirect('zanders/orders/order', array('number' => $this->getRequest()->getParam('number')));
    }
}
