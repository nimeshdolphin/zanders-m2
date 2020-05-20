<?php declare(strict_types=1);


namespace Zanders\Related\Controller\Index;

use Magento\Catalog\Api\ProductRepositoryInterface;

class Replacement extends \Magento\Framework\App\Action\Action
{

	protected $productRepositoryInterface;
    protected $resultPageFactory;
    protected $jsonHelper;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     */
    public function __construct(
    	ProductRepositoryInterface $productRepositoryInterface,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Psr\Log\LoggerInterface $logger
    ) {
    	$this->productRepositoryInterface = $productRepositoryInterface;
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper = $jsonHelper;
        $this->logger = $logger;
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
        	$productId = 53508;
        	$product = $this->productRepositoryInterface->getById($productId);
            $related = $product->getRelatedProducts();
 			$upsell = $product->getUpSellProducts();	

 			$relatedProduct = array();
            if (count($related)) {
                foreach ($related as $item) {
                	$product = $this->productRepositoryInterface->getById($item->getId());
                    $relatedProduct['id'] = $product->getId();
                    $relatedProduct['name'] = $product->getName();
                }
            }

            $upsellProduct = array();
            if (count($upsell)) {
                foreach ($upsell as $item) {
                	$product = $this->productRepositoryInterface->getById($item->getId());
                    $upsellProduct['id'] = $product->getId();
                    $upsellProduct['name'] =  $product->getName();
                }
            }

            $related  = array('related'=>$relatedProduct,'upsell'=>$upsellProduct);
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
