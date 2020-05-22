<?php declare(strict_types=1);


namespace Zanders\Related\Controller\Index;

use Magento\Catalog\Api\ProductRepositoryInterface;

class Replacement extends \Magento\Framework\App\Action\Action
{

    protected $request;
    protected $_helper;
    protected $_productCollectionFactory;
	protected $productRepositoryInterface;
    protected $resultPageFactory;
    protected $jsonHelper;
    protected $imageHelperFactory;
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Dolphin\DynamicRules\Helper\Data $helper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
    	ProductRepositoryInterface $productRepositoryInterface,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Catalog\Helper\ImageFactory $imageHelperFactory

    ) {
        $this->request = $request;
        $this->_helper = $helper;
        $this->_productCollectionFactory = $productCollectionFactory;
    	$this->productRepositoryInterface = $productRepositoryInterface;
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper = $jsonHelper;
        $this->logger = $logger;
        $this->imageHelperFactory = $imageHelperFactory;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try 
        {	
        	$productId = $this->request->getParam('id');
            $product = $this->productRepositoryInterface->getById($productId);
            $related = $product->getRelatedProducts();
            $upsell = $product->getUpSellProducts();    

            $defaultProducts = $product->getRelatedProductIds();

            $dynamicproducts = $this->_helper->dynamicProducts($productId);
            $DCollection = $this->_productCollectionFactory->create();
            $DCollection->addFieldToFilter('entity_id', ['in' => $dynamicproducts]);
            $DCollection->getSelect()->order("find_in_set(entity_id,'" . implode(',', $dynamicproducts) . "')");

            $DefaultCollection = $this->_productCollectionFactory->create();
            $DefaultCollection->addFieldToFilter('entity_id', ['in' => $defaultProducts]);
            $DefaultCollection->getSelect()->order("find_in_set(entity_id,'" . implode(',', $defaultProducts) . "')");

            $default = [];
            foreach ($defaultProducts as $pro) {
                if (in_array($pro, $DefaultCollection->getAllIds())) {
                    $default[] = $pro;
                }
            }

            $ids = [];
            foreach ($this->_helper->dynamicProducts($productId) as $pro) {
                if (in_array($pro, $DCollection->getAllIds())) {
                    $ids[] = $pro;
                }
            }

            if (count($dynamicproducts) > 0) {
                $merged_ids = array_merge($default, $ids); // here we merge both collection ids.
            } else {
                $merged_ids = $default;
            }

        	$product = $this->productRepositoryInterface->getById($productId);
            $related = $product->getRelatedProducts();
 			$upsell = $product->getUpSellProducts();	

 			$allRealated = array();
            if (count($related)) {
                foreach ($merged_ids as $item) {
                    $relatedProduct = array();
                	$product = $this->productRepositoryInterface->getById($item);
                    if(!$product->isVisibleInSiteVisibility()) continue;
                    $relatedProduct['id'] = $product->getId();
                    $relatedProduct['name'] = $product->getName();
                    $imageUrl = $this->imageHelperFactory->create()->init($product, 'related_products_list')->getUrl();
                    $relatedProduct['image'] = $imageUrl;
                    $relatedProduct['itemnumber'] = $product->getSku();
                    $relatedProduct['upc'] = $product->getUpc();          
                    $relatedProduct['image'] = $imageUrl;
                    $relatedProduct['url'] = $product->getProductUrl();
                    $allRealated[] = $relatedProduct;
                }
            }

            $allupsell = array();
            if (count($upsell)) {
                foreach ($upsell as $item) {
                    $upsellProduct = array();
                	$product = $this->productRepositoryInterface->getById($item->getId());
                    if(!$product->isVisibleInSiteVisibility()) continue;
                    $upsellProduct['id'] = $product->getId();
                    $upsellProduct['name'] =  $product->getName();
                    $imageUrl = $this->imageHelperFactory->create()->init($product, 'related_products_list')->getUrl();
                    $upsellProduct['image'] = $imageUrl;
                    $upsellProduct['itemnumber'] = $product->getSku();
                    $upsellProduct['upc'] = $product->getUpc();
                    $relatedProduct['url'] = $product->getProductUrl();
                    $allupsell[] = $upsellProduct;
                }
            }

            $related  = array('related'=>$allRealated,'upsell'=>$allupsell);
            return $this->jsonResponse($related);

        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return $this->jsonResponse($e->getMessage());
        } catch (\Exception $e) 
        {
            $this->logger->critical($e);
            return $this->jsonResponse($e->getMessage());
        }
    }

    /**
     * Create json response
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function jsonResponse($response = '')
    {
        return $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($response)
        );
    }
}
