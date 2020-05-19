<?php
namespace Zanders\Sports\Block\Quote;

use Magento\Framework\View\Element\Template;

class Create extends Template
{
    protected $_productloader;
    protected $request;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Customer\Model\Session $customer,
        array $data = []
    ) {
        $this->_productloader = $_productloader;
        $this->request = $request;
        $this->customer = $customer;
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
        $customer = $this->customer;
        $customerName =  $customer->getName();
        echo $customerId = $customer->getId(); exit;
    }

    public function getProduct()
    {
        $id = $this->request->getParam('product');
        return $this->_productloader->create()->load($id);
    }

}