<?php
namespace Zanders\Sports\Block\Quote;

use Magento\Framework\View\Element\Template;

class Create extends Template
{
    protected $_productloader;
    protected $request;
    protected $customerSession;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        $this->customerRepository = $customerRepository;
        $this->addressRepository = $addressRepository;
        $this->_productloader = $_productloader;
        $this->request = $request;
        $this->customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    public function _prepareLayout()
    {
       $this->pageConfig->getTitle()->set(__('Create a Quote for a Customer'));
       return parent::_prepareLayout();
    }

    /**
     * $customerId
     */
    public function getDefaultShippingAddress()
    {
        ///echo $this->customerSession->getCustomerId();exit;
        $address = array();
        $customerId = 41502;
        $customer = $this->customerRepository->getById($customerId);
        $shippingAddressId  = $customer->getDefaultShipping();
        try {
            $address = $this->addressRepository->getById($shippingAddressId);
        } catch (\Exception $e) {

        }
        print_r($address);exit;
        return $address;
    }

    public function getProduct()
    {
        $id = $this->request->getParam('product');
        return $this->_productloader->create()->load($id);
    }

}