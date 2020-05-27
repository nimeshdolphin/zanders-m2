<?php
/**
 * @category   Zanders
 * @package    Zanders_FlatRate
 */

namespace Zanders\FlatRate\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;

class Zandersship extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = 'zandersship';

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\Error $rateResultError,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Zanders\FlatRate\Model\FlatRateFactory $flatRateFactory,
        \Zanders\FlatRate\Helper\Data $flatRateHelper,
        array $data = []
    )
    {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->_rateResultError = $rateResultError;
        $this->_customerSession = $customerSession;
        $this->_customerFactory = $customerFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->_productRepository = $productRepository;
        $this->_encryptor = $encryptor;
        $this->_flatRateFactory = $flatRateFactory;
        $this->_flatRateHelper = $flatRateHelper;

        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        return ['zandersship' => $this->getConfigData('name')];
    }

    /**
     * @param RateRequest $request
     * @return bool|Result
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }
        try {
            /** @var \Magento\Shipping\Model\Rate\Result $result */
            $result = $this->_rateResultFactory->create();

            $customer = $this->_customerFactory->create();
            $customer->load($this->_customerSession->getCustomer()->getId());

            $quote = $this->_checkoutSession->getQuote();

            $elliottItem = new \Zanders_Elliott_Model_Elliott_Item();
            $elliottCustomer = new \Zanders_Elliott_Model_Elliott_Customer();

            $customerInfo = $elliottCustomer->GetCustomerInfo(array(
                'CustomerNo' => $customer->getNumber(),
                'GetCustomerData' => "Y",
            ));

            $customerXml = simplexml_load_string($customerInfo->GetCustomerInfoResult->CustomerInfo->any);
            $customerTerms = trim($customerXml->NewDataSet->ARCUSFIL->CUS_TERMS_CD);

            $flatRateExtras = $this->_flatRateHelper->getFlatRateExtras();
            $shipping = 0.00;
            $pickup = 0.00;
            $shippableTotal = 0.00;
            $additionalCharges = 0.00;
            $addAdditionalCharges = false;
            $powderWeight = 0.00;
            $primerWeight = 0.00;
            $handlingFee = 0.00;
            $freightFee = 0.00;

            $allItems = $request->getAllItems();

            foreach ($allItems as $item) {
                $product = $this->_productRepository->getById($item->getProductId());
                $data = $elliottItem->ReadOneItem(array('ITEM_NO' => $product->getSku()));
                if ($data->ReadOneItemResult->ReturnCode == 0) {
                    $available = intval($data->ReadOneItemResult->ItemRecord->ITEM_QTY_ON_HAND) - intval($data->ReadOneItemResult->ItemRecord->ITEM_QTY_ALLOC);
                    $shippableQty = 0;
                    if ($available >= $item->getQty()) {
                        $shippableTotal += $item->getQty() * $item->getPrice();
                        $shippableQty = $item->getQty();
                    } else {
                        if ($available > 0) {
                            $shippableQty = $available;
                            $shippableTotal += $available * $item->getPrice();
                        }
                    }
                    switch ($data->ReadOneItemResult->ItemRecord->ITEM_ROUTING_NO) {
                        case 'CT':
                            $addAdditionalCharges = true;
                            break;
                        case 'PA':
                            $additionalCharges += $this->_scopeConfig->getValue('sports/zandersdefaults/pa') * $shippableQty;
                            break;
                        case 'PD':
                            $powderWeight += ($data->ReadOneItemResult->ItemRecord->ITEM_WEIGHT * $shippableQty);
                            break;
                        case 'PR':
                            $primerWeight += ($data->ReadOneItemResult->ItemRecord->ITEM_WEIGHT * $shippableQty);
                            break;
                        case 'SF':
                            $addAdditionalCharges = true;
                            break;
                        case 'SH':
                            $additionalCharges += $this->_scopeConfig->getValue('sports/zandersdefaults/sh') * $shippableQty;
                            break;
                        case 'W1':
                            $additionalCharges += $this->_scopeConfig->getValue('sports/zandersdefaults/w1') * $shippableQty;
                            break;
                        case 'W2':
                            $additionalCharges += $this->_scopeConfig->getValue('sports/zandersdefaults/w2') * $shippableQty;
                            break;
                        case 'CB':
                            $additionalCharges += $this->_scopeConfig->getValue('sports/zandersdefaults/cb') * $shippableQty;
                            break;
                    }
                }
            }

            if ($shippableTotal < floatval($this->_scopeConfig->getValue('sports/zandersdefaults/shipfree'))) {
                $freightFee = $this->_scopeConfig->getValue('sports/zandersdefaults/shipunderfee');
                $shipping += $freightFee;
            }

            if ($shippableTotal < floatval($this->_scopeConfig->getValue('sports/zandersdefaults/shipextra')) && $customer->getData('remove_add_ship') != 1) {
                $handlingFee = $this->_scopeConfig->getValue('sports/zandersdefaults/shipextrafee');
                $shipping += $handlingFee;
            }

            if ($customer->getFlatRate() == 1) {
                $flatRateTier = $this->_flatRateFactory->create()->load($customer->getFlatRateTier());
                if ($flatRateTier->getId()) {
                    $shipping = floatval($flatRateTier->getAccessory());
                    if ($customer->getAddFfls() == '1') {
                        $shipping = floatval($flatRateTier->getFirearm());
                    }
                } else {
                    $shipping = floatval($this->_scopeConfig->getValue('sports/zandersdefaults/shipflatfee'));
                    if ($customer->getAddFfls() == 1) {
                        $shipping = floatval($this->_scopeConfig->getValue('sports/zandersdefaults/shipflatfirearmfee'));
                    }
                }

                $statesList = explode(
                    ',',
                    $this->_scopeConfig->getValue('sports/zandersdefaults/exceptionstates')
                );
                if (in_array($request->getDestRegionCode(), $statesList)) {
                    $shipping = 0.00;
                }
            }

            if ($shippableTotal == 0.00) {
                $shipping = 0.00;
                $addAdditionalCharges = true;
            }

            $shipping += $additionalCharges;

            if ($powderWeight > 0.00) {
                $shipping += floatval($this->_scopeConfig->getValue('sports/zandersdefaults/hazmat')) * ceil($powderWeight / 45);
            }

            if ($primerWeight > 0.00) {
                $shipping += floatval($this->_scopeConfig->getValue('sports/zandersdefaults/hazmat')) * ceil($primerWeight / 45);
            }

            $isCCustomer = false;
            if ($customerTerms == 'C1' || $customerTerms == 'C2' || $customerTerms == 'C3') {
                $addAdditionalCharges = true;
                $isCCustomer = true;
            }

            $actualCharges = '-(Actual Freight Charges Apply)';
            if ($customer->getFlatRate() == '1') {
                $actualCharges = '';
            }

            $addAdditionalCharge = '';

            $paymentInfo = $quote->getPayment()->getAdditionalInformation();
            if (array_key_exists('notes', $paymentInfo)) {
                $paymentNotes = $this->_encryptor->decrypt($paymentInfo['notes']);
                if ($paymentNotes == 'COD Certified Funds' || $isCCustomer) {
                    $codAmount = floatval($this->_scopeConfig->getValue('sports/zandersdefaults/cod'));
                    $addAdditionalCharge = ' +(COD  ' . $codAmount . ')';
                }
            }

            if (!empty($handlingFee)) {
//                $addAdditionalCharge.=' +(Handling Fee '.$handlingFee.')'; //already added to $shipping
            }
            if (!empty($freightFee)) {
//                $addAdditionalCharge.=' +(Freight Fee '.$freightFee.')'; //@TODO: missing fee !!!!!
            }
            $additionalMessage = 'See Freight Policy for Additional Charges';


            /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
            $method = $this->_rateMethodFactory->create();
            $method->setCarrier($this->_code);
            $method->setMethod('BW');
            $method->setMethodTitle('Best Way' . $addAdditionalCharge . ($addAdditionalCharges ? '-(' . $additionalMessage . ')' : ''));
            $method->setCost($shipping);
            $method->setPrice($shipping);
            $result->append($method);

            if ($customer->getShipper() == '50111') {
                unset($method);

                /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
                $method = $this->_rateMethodFactory->create();
                $method->setCarrier($this->_code);
                $method->setMethod('FH');
                $method->setMethodTitle('FedEx Home' . $actualCharges);
                $method->setCost($shipping);
                $method->setPrice($shipping);
                if ($customer->getDropshipallow() == '1' &&
                    $customer->getFlatRate() == '1' &&
                    array_key_exists('FH', $flatRateExtras)
                ) {
                    $method->setCost($shipping + floatval($flatRateExtras['FH']));
                    $method->setPrice($shipping + floatval($flatRateExtras['FH']));
                    $method->setCarrierTitle('Y');
                }
                $result->append($method);

                unset($method);

                /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
                $method = $this->_rateMethodFactory->create();
                $method->setCarrier($this->_code);
                $method->setMethod('FG');
                $method->setMethodTitle('FedEx Ground' . $actualCharges);
                $method->setCost($shipping);
                $method->setPrice($shipping);
                if ($customer->getDropshipallow() == '1' &&
                    $customer->getFlatRate() == '1' &&
                    array_key_exists('FG', $flatRateExtras)
                ) {
                    $method->setCost($shipping + floatval($flatRateExtras['FG']));
                    $method->setPrice($shipping + floatval($flatRateExtras['FG']));
                    $method->setCarrierTitle('Y');
                }
                $result->append($method);

                unset($method);

                /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
                $method = $this->_rateMethodFactory->create();
                $method->setCarrier($this->_code);
                $method->setMethod('F1');
                $method->setMethodTitle('FedEx Next Day Air' . $actualCharges);
                $method->setCost($shipping);
                $method->setPrice($shipping);
                if ($customer->getDropshipallow() == '1' &&
                    $customer->getFlatRate() == '1' &&
                    array_key_exists('F1', $flatRateExtras)
                ) {
                    $method->setCost($shipping + floatval($flatRateExtras['F1']));
                    $method->setPrice($shipping + floatval($flatRateExtras['F1']));
                    $method->setCarrierTitle('Y');
                }
                $result->append($method);

                unset($method);

                /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
                $method = $this->_rateMethodFactory->create();
                $method->setCarrier($this->_code);
                $method->setMethod('F2');
                $method->setMethodTitle('FedEx 2nd Day Air' . $actualCharges);
                $method->setCost($shipping);
                $method->setPrice($shipping);
                if ($customer->getDropshipallow() == '1' &&
                    $customer->getFlatRate() == '1' &&
                    array_key_exists('F2', $flatRateExtras)
                ) {
                    $method->setCost($shipping + floatval($flatRateExtras['F2']));
                    $method->setPrice($shipping + floatval($flatRateExtras['F2']));
                    $method->setCarrierTitle('Y');
                }
                $result->append($method);
            } else {
                if ($customer->getDropshipallow() == '1') {
                    unset($method);

                    /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
                    $method = $this->_rateMethodFactory->create();
                    $method->setCarrier($this->_code);
                    $method->setMethod('UM');
                    $method->setMethodTitle('UPS Sure Post' . $actualCharges);
                    $method->setCost($shipping);
                    $method->setPrice($shipping);
                    if ($customer->getDropshipallow() == '1' &&
                        $customer->getFlatRate() == '1' &&
                        array_key_exists('UM', $flatRateExtras)
                    ) {
                        $method->setCost($shipping + floatval($flatRateExtras['UM']));
                        $method->setPrice($shipping + floatval($flatRateExtras['UM']));
                    }
                    if ($customer->getFlatRate() == '1') {
                        $method->setCarrierTitle('Y');
                    }
                    $result->append($method);

                    unset($method);

                    /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
                    $method = $this->_rateMethodFactory->create();
                    $method->setCarrier($this->_code);
                    $method->setMethod('UG');
                    $method->setMethodTitle('UPS Ground' . $actualCharges);
                    $method->setCost($shipping);
                    $method->setPrice($shipping);
                    if ($customer->getDropshipallow() == '1' &&
                        $customer->getFlatRate() == '1' &&
                        array_key_exists('UG', $flatRateExtras)
                    ) {
                        $method->setCost($shipping + floatval($flatRateExtras['UG']));
                        $method->setPrice($shipping + floatval($flatRateExtras['UG']));
                    }
                    if ($customer->getFlatRate() == '1') {
                        $method->setCarrierTitle('Y');
                    }
                    $result->append($method);
                }

                unset($method);

                /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
                $method = $this->_rateMethodFactory->create();
                $method->setCarrier($this->_code);
                $method->setMethod('U2');
                $method->setMethodTitle('UPS Second Day Air' . $actualCharges);
                $method->setCost($shipping);
                $method->setPrice($shipping);
                if ($customer->getDropshipallow() == '1' &&
                    $customer->getFlatRate() == '1' &&
                    array_key_exists('U2', $flatRateExtras)
                ) {
                    $method->setCost($shipping + floatval($flatRateExtras['U2']));
                    $method->setPrice($shipping + floatval($flatRateExtras['U2']));
                    $method->setCarrierTitle('Y');
                }
                $result->append($method);

                unset($method);

                /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
                $method = $this->_rateMethodFactory->create();
                $method->setCarrier($this->_code);
                $method->setMethod('U1');
                $method->setMethodTitle('UPS Next Day Air' . $actualCharges);
                $method->setCost($shipping);
                $method->setPrice($shipping);
                if ($customer->getDropshipallow() == '1' &&
                    $customer->getFlatRate() == '1' &&
                    array_key_exists('U1', $flatRateExtras)
                ) {
                    $method->setCost($shipping + floatval($flatRateExtras['U1']));
                    $method->setPrice($shipping + floatval($flatRateExtras['U1']));
                    $method->setCarrierTitle('Y');
                }
                $result->append($method);
            }

            unset($method);

            // if(empty($pickup)){
            //     $pickup=$this->_scopeConfig->getValue('sports/zandersdefaults/shipunderfee');
            // }
            /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
            $method = $this->_rateMethodFactory->create();
            $method->setCarrier($this->_code);
            $method->setMethod('PU');
            $method->setMethodTitle('Pick Up');
            $method->setCost($pickup);
            $method->setPrice($pickup);

            $result->append($method);

            return $result;
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            exit;
            $result->setError(true);

            unset($method);
            /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
            $method = $this->_rateMethodFactory->create();
            $method->setCarrier($this->_code);
            $method->setCarrierTitle($this->_scopeConfig->getValue('carriers/' . $this->_code . '/title'));
            $method->setErrorMessage($e->getMessage());
            $result->append($method);
            return $result;
        }
    }
}