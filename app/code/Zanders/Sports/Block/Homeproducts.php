<?php
namespace Zanders\Sports\Block;

class Homeproducts extends \Magento\Catalog\Block\Product\AbstractProduct
{
    protected $_catalogProductVisibility;

    protected $_productCollectionFactory;

    protected $_categoryFactory;

    protected $urlHelper;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Zanders\Sports\Helper\Config $scopeConfig,
        array $data = []
    ) {
        $this->_categoryFactory = $categoryFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->urlHelper = $urlHelper;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context, $data);
    }

    public function getFeaturedProducts() {
        $skuConfig = $this->scopeConfig->getConfig('sports/homepage/featured');
        $sku = explode(',',$skuConfig);
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*')
                ->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds())
                ->addAttributeToFilter('sku', array('in' => $sku));
        return $collection;
    }

    public function getDailySpecials() {
        $skuConfig = $this->scopeConfig->getConfig('sports/homepage/daily');
        $sku = explode(',',$skuConfig);
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*')
                ->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds())
                ->addAttributeToFilter('sku', array('in' => $sku));
        return $collection;
    }

    public function getHotsaleProducts() {
        $skuConfig = $this->scopeConfig->getConfig('sports/homepage/topsellers');
        $sku = explode(',',$skuConfig);
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*')
                ->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds())
                ->addAttributeToFilter('sku', array('in' => $sku));
        return $collection;
    }
}