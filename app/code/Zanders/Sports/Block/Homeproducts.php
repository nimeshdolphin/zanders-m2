<?php
namespace Zanders\Sports\Block;

class Homeproducts extends \Magento\Catalog\Block\Product\AbstractProduct
{
    protected $_catalogProductVisibility;

    protected $_productCollectionFactory;

    protected $_categoryFactory;

    protected $urlHelper;

    protected $_customerSession;

    protected $stockState;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\CatalogInventory\Api\StockStateInterface $stockState,
        array $data = []
    ) {
        $this->_categoryFactory = $categoryFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->urlHelper = $urlHelper;
        $this->scopeConfig = $scopeConfig;
        $this->_customerSession = $customerSession;
        $this->stockState = $stockState;
        parent::__construct($context, $data);
    }

    public function getFeaturedProducts() {
        //$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        //$this->scopeConfig->getValue('', $storeScope);
        $skuConfig = "00002, G2796066CT,GH002,A05400,CC0001GRC,G5080512,GH001L,A03190,CC0002YD,G2837218,GH001T,A01950,CC0021CB,G2796066TNS,GH004L,A02300,RP0002CPV";
        $sku = explode(',',$skuConfig);
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*')
                ->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds())
                ->addAttributeToFilter('sku', array('in' => $sku));
        return $collection;
    }

    public function getDailySpecials() {
        //$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        //$this->scopeConfig->getValue('', $storeScope);
        $skuConfig = "GCF930242,00002";
        $sku = explode(',',$skuConfig);
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*')
                ->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds())
                ->addAttributeToFilter('sku', array('in' => $sku));
        return $collection;
    }

    public function getHotsaleProducts() {
        //$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        //$this->scopeConfig->getValue('', $storeScope);
        $skuConfig = "G26993,G16950,GPP410LNTXR,GPP411LNBB,GDTSPORTM2,GRFTM160,AVAR15AMW,AVAR15MBB,AVAR15AMK,18700H,15020,15000,H3103,613944,613948,150142,PR3124NM,HE510CGR,GSUB9G17HC,SVL620BT,MPAR15";
        $sku = explode(',',$skuConfig);
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*')
                ->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds())
                ->addAttributeToFilter('sku', array('in' => $sku));
        return $collection;
    }

    public function getCustomerGroupId(){
        $customerGroup = '';
        if($this->_customerSession->isLoggedIn()){
            $customerGroup = $this->_customerSession->getCustomer()->getGroupId();
        }
        return $customerGroup;
    }

    public function getCustomerLogin(){
        if($this->_customerSession->isLoggedIn()){
            return true;
        }
        return false;
    }

    public function getStockQty($productId, $websiteId = null)
    {
        return $this->stockState->getStockQty($productId, $websiteId);
    }
}