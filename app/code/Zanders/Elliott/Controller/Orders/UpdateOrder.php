<?php
/**
 * @category   Zanders
 * @package    Zanders_Elliott
 */

namespace Zanders\Elliott\Controller\Orders;

use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Filesystem;
use Magento\Framework\Phrase;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Zend_Mail;
use Zend_Mime;

/**
 * Class UpdateOrder
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UpdateOrder extends \Zanders\Elliott\Controller\AbstractAccount implements CsrfAwareActionInterface, HttpPostActionInterface
{
    protected $_notallowed = array('C', 'A', 'F');

    protected $customerSession;
    protected $customerFactory;
    protected $urlInterface;
    protected $uploaderFactory;
    protected $fileSystem;
    protected $region;
    protected $dateTime;
    protected $formKey;
    protected $sportsHelper;
    protected $sportsConfigHelper;
    protected $elliottHelper;
    protected $messageManager;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\UrlInterface $urlInterface,
        UploaderFactory $uploaderFactory,
        Filesystem $fileSystem,
        \Magento\Directory\Model\Region $region,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Zanders\Sports\Helper\Data $sportsHelper,
        \Zanders\Sports\Helper\Config $sportsConfigHelper,
        \Zanders\Elliott\Helper\Data $elliottHelper,
        array $data = []
    )
    {
        $this->customerSession = $customerSession;
        $this->customerFactory = $customerFactory;
        $this->urlInterface = $urlInterface;
        $this->uploaderFactory = $uploaderFactory;
        $this->fileSystem = $fileSystem;
        $this->region = $region;
        $this->dateTime = $dateTime;
        $this->formKey = $formKey;
        $this->sportsHelper = $sportsHelper;
        $this->sportsConfigHelper = $sportsConfigHelper;
        $this->elliottHelper = $elliottHelper;
        $this->messageManager = $context->getMessageManager();
        parent::__construct($context);
    }

    public function execute()
    {
        try {
            $shipping = $this->getRequest()->getParam('shipping');
            $order_no = $this->getRequest()->getParam('order_no');
            $region_id = $this->getRequest()->getParam('region_id');
            $country_id = $this->getRequest()->getParam('country_id');

            $customer = $this->customerFactory->create();
            $customer->load($this->customerSession->getCustomer()->getId());

            $elliottOrderInquiry = new \Zanders_Elliott_Model_Elliott_Orderinquiry();
            $order = $elliottOrderInquiry->GetOneOrderDetail(array('orderNo' => $order_no));

            if ($order->GetOneOrderDetailResult->ReturnCode == 0) {
                $ordxml = simplexml_load_string($order->GetOneOrderDetailResult->OrderDetail->any);
                if ($ordxml->NewDataSet->OrderHeader->ORDER_CUSTOMER_NO == $customer->getNumber()) {
                    if (!in_array($ordxml->NewDataSet->OrderHeader->ORDER_RELEASE_FLAG, $this->_notallowed)) {

                        $orderheader = array();
                        $orderheader['ORDER_NO'] = $order_no;

                        $elliottShipToAddresses = new \Zanders_Elliott_Model_Elliott_Shiptoaddresses();
                        $elliottCustomer = new \Zanders_Elliott_Model_Elliott_Customer();
                        $customerinfo = $elliottCustomer->GetCustomerInfo(
                            array('CustomerNo' => $shipping["customer_number"], 'GetCustomerData' => "Y")
                        );

                        $customerxml = simplexml_load_string($customerinfo->GetCustomerInfoResult->CustomerInfo->any);

                        $fflexp = explode('-', $shipping["fflexp"]);

                        if (count($fflexp) != 3) {
                            $fflexp = array('0000', '00', '00');
                        }

                        $shiptonum = '';
                        if (strlen($shipping["company"]) > 0) {
                            $searchinfo = array('SearchShipToInput' => array(
                                'SearchCustomerNo' => $shipping["customer_number"],
                                'SearchFieldName' => 'XREF',
                                'SearchValue' => $shipping["company"]
                            ));
                            $info = $elliottShipToAddresses->SearchShipTo($searchinfo);
                            if ($info->SearchShipToResult->ReturnCode == 0 && intval($info->SearchShipToResult->NoOfSearchResult) > 0) {
                                $addrec = get_object_vars($info->SearchShipToResult->ShipToRecord->shipToRecord);
                                $shiptonum = $addrec["ShipToNo"];

                                $state = $this->region->load($region_id);
                                $fflshiptoaddress = array('ChangeShipToInput' => array(
                                    'ShipToCustomerNo' => $shipping['customer_number'],
                                    'ShipToNo' => strval($shiptonum),
                                    'ShipToName' => $shipping['firstname'],
                                    'ShipToAddress1' => $shipping['street'][0],
                                    'ShipToAddress2' => $shipping['street'][1],
                                    'ShipToCity' => $shipping['city'],
                                    'ShipToState' => $state->getCode(),
                                    'ShipToStateCode' => $state->getId(),
                                    'ShipToZipCode' => $shipping['postcode'],
                                    'ShipToFFLNo' => $shipping['company'],
                                    'ShipToXrefNo' => $shipping['company'],
                                    'ShipToOrderLocation' => '01',
                                    'ShipToShipViaCode' => 'BW',
                                    'ShipToExemptExpireDate' => 0,
                                    'ShipToFFLExpDate' => $shipping['fflexp'],
                                    'ShipToDeliveryLeadTime' => 0.0
                                ));

                                $fflchangeresult = $elliottShipToAddresses->ChangeShipTo($fflshiptoaddress);
                                if ($fflchangeresult->ChangeShipToResult->ReturnCode != 0) {
                                    $message = explode('^', $fflchangeresult->ChangeShipToResult->ReturnMsg);
                                    $this->messageManager->addErrorMessage($message[2]);
                                }

                            } else {
                                $region = $this->region->load($region_id);

                                $shiptoaddress = array('AddShipToInput' => array(
                                    'ShipToCustomerNo' => $shipping["customer_number"],
                                    'ShipToName' => $shipping["firstname"],
                                    'ShipToAddress1' => $shipping['street'][0],
                                    'ShipToAddress2' => $shipping['street'][1],
                                    'ShipToCity' => $shipping["city"],
                                    'ShipToState' => $region->getCode(),
                                    'ShipToStateCode' => $region->getId(),
                                    'ShipToZipCode' => $shipping["postcode"],
                                    'ShipToFFLNo' => $shipping["company"],
                                    'ShipToXrefNo' => $shipping["company"],
                                    'ShipToOrderLocation' => '01',
                                    'ShipToShipViaCode' => 'BW',
                                    'ShipToExemptExpireDate' => 0,
                                    'ShipToFFLExpDate' => $fflexp[0] . '-' . $fflexp[1] . '-' . $fflexp[2],
                                    'ShipToDeliveryLeadTime' => 0.0,
                                    'ShipToTaxCode1' => strval($customerxml->NewDataSet->ARCUSFIL->CUS_TX_CD1)
                                ));

                                $result = $elliottShipToAddresses->AddShipTo($shiptoaddress);
                                $addrec = get_object_vars($result->AddShipToResult->ShipToRecord->shipToRecord);
                                $shiptonum = $addrec["ShipToNo"];
                            }
                        }

                        $orderheader['ORDER_SHIP_TO_NO'] = $shiptonum;
                        $orderdate = strval($ordxml->NewDataSet->OrderHeader->ORDER_SHIPPING_DATE);

                        //$orderheader['ORDER_FRGHT_PAY_CODE'] = " ";
                        $orderheader['ORDER_DATE'] = '0001-01-01';
                        $orderheader['ORDER_CHECK_DATE'] = '0001-01-01';
                        $orderheader['ORDER_DATE_PICKED'] = '0001-01-01';
                        $orderheader['ORDER_DATE_BILLED'] = '0001-01-01';
                        $orderheader['ORDER_INVOICE_DATE'] = '0001-01-01';
                        $orderheader['ORDER_POSTED_DATE'] = '0001-01-01';
                        $orderheader['ORDER_SHIP_ACK_DATE'] = '0001-01-01';
                        $orderheader['ORDER_PICK_TICK_CUT'] = '0001-01-01';
                        $orderheader['ORDER_DATE_ENTERED'] = '0001-01-01';
                        $orderheader['ORDER_SHIPPING_DATE'] = substr($orderdate, 0, 4) . '-' . substr($orderdate, 4, 2) . '-' . substr($orderdate, 6, 2);
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
                        $orderheader['ORDER_SHIP_INSTRUC1'] = $shipping["customername"];
                        $orderheader['ORDER_SHIP_INSTRUC2'] = $shipping["customertelephone"];

                        $elliottOrder = new \Zanders_Elliott_Model_Elliott_Order();
                        $response = $elliottOrder->ChangeHeader(array('ChangeHeaderInput' => $orderheader));

                        if ($response->ChangeHeaderResult->ReturnCode != 0) {
                            $this->messageManager->addErrorMessage(__('There was a problem changing the FFL ship to on this order'));
                        }

                        $elliottNote = new \Zanders_Elliott_Model_Elliott_Note();
                        $fflnote = array('AddNoteInput' => array('AddNoteInput' => array(
                            'FileName' => 'CPORDHDR',
                            'FileReferenceNumber' => $order_no,
                            'Folder' => '',
                            'CreateDateTime' => $this->dateTime->date('c'),
                            'NoteType' => 'RBASSM',
                            'Topic' => 'Customer Info',
                            'Content1' => $shipping["customername"],
                            'Content2' => $shipping["customertelephone"],
                            'Content3' => '',
                            'Content4' => '',
                            'Content5' => '',
                            'Content6' => '',
                            'Content7' => '',
                            'Content8' => '',
                            'Content9' => '',
                            'Content10' => '',
                            'ElliottUser' => ''
                        )));
                        $elliottNote->AddNote($fflnote);

                        if ($shipping["cfd"] != '') {
                            $CFDnote = array('AddNoteInput' => array('AddNoteInput' => array(
                                'FileName' => 'CPORDHDR',
                                'FileReferenceNumber' => $order_no,
                                'Folder' => '',
                                'CreateDateTime' => $this->dateTime->date('Y-m-d'),
                                'NoteType' => '',
                                'Topic' => 'CFD#: ' . $shipping["cfd"],
                                'Content1' => 'CFD#: ' . $shipping["cfd"],
                                'Content2' => '',
                                'Content3' => '',
                                'Content4' => '',
                                'Content5' => '',
                                'Content6' => '',
                                'Content7' => '',
                                'Content8' => '',
                                'Content9' => '',
                                'Content10' => '',
                                'ElliottUser' => ''
                            )));
                            $elliottNote->AddNote($CFDnote);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        $this->_redirect('zanders/orders/order', array('number' => $order_no));
    }

    /**
     * @inheritDoc
     */
    public function createCsrfValidationException(
        RequestInterface $request
    ): ?InvalidRequestException
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $order_no = $this->getRequest()->getParam('order_no');
        $resultRedirect->setPath('zanders/orders/order', array('number' => $order_no));

        return new InvalidRequestException(
            $resultRedirect,
            [new Phrase('Invalid Form Key. Please refresh the page.')]
        );
    }

    /**
     * @inheritDoc
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return null;
    }
}
