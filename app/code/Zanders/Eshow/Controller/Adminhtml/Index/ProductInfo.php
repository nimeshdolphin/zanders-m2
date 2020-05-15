<?php
/**
 * @category   Zanders
 * @package    Zanders_Eshow
 */

namespace Zanders\Eshow\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\Result\JsonFactory;
use Zanders\Eshow\Model\Eshow;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product as ProductResourceModel;

class ProductInfo extends \Magento\Backend\App\Action
{
    protected $eshowModel;
    protected $productResourceModel;
    protected $productFactory;
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    public function __construct(
        Action\Context $context,
        JsonFactory $resultJsonFactory,
        Eshow $eshowModel,
        ProductFactory $productFactory,
        ProductResourceModel $productResourceModel
    )
    {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->eshowModel = $eshowModel;
        $this->productFactory = $productFactory;
        $this->productResourceModel = $productResourceModel;
    }

    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();

        $data = $this->getRequest()->getPost('data');
        $lineData = explode("\n", $data);

        $response = [];
        foreach ($lineData as $line) {
            $productData = array_map('trim', explode(',', $line));
            $product = $this->productFactory->create();
            $productId = $this->productResourceModel->getIdBySku($productData[0]);
            if ($productId) {
                $product->load($productId);
                $response[] = [
                    'sku' => $product->getSku(),
                    'min_qty' => (isset($productData[1]) && $productData[1] > 0) ? (int)$productData[1] : 0,
                    'max_qty' => (isset($productData[2]) && $productData[2] > 0) ? (int)$productData[2] : 0,
                    'price' => (isset($productData[3]) && $productData[3] > 0) ? $productData[3] : $product->getPrice(),
                    'desc' => (isset($productData[4]) && trim($productData[4]) != '') ? $productData[4] : trim($product->getName())
                ];
            }
        }
        $resultJson->setData($response);
        $this->_actionFlag->set('', self::FLAG_NO_POST_DISPATCH, true);
        return $resultJson;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zanders_Eshow::eshow');
    }
}
