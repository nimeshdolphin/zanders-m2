<?php
/**
 * @category   Zanders
 * @package    Zanders_Eshow
 */

namespace Zanders\Eshow\Block\Adminhtml\Index;

use Zanders\Eshow\Model\ResourceModel\EshowPurchasedItem as EshowPurchasedItemResource;
use Magento\Catalog\Model\ProductFactory;

class PurchasedItem extends \Magento\Backend\Block\Template
{
    protected $_template = 'Zanders_Eshow::purchase_items_table.phtml';

    /**
     * @var EshowPurchasedItemResource $eshowPurchasedItemResource
     */
    protected $eshowPurchasedItemResource;

    protected $productFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param ProductFactory $productFactory
     * @param EshowPurchasedItemResource $eshowPurchasedItemResource
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        ProductFactory $productFactory,
        EshowPurchasedItemResource $eshowPurchasedItemResource
    )
    {
        $this->productFactory = $productFactory;
        $this->eshowPurchasedItemResource = $eshowPurchasedItemResource;
        parent::__construct($context);
    }

    public function getPurchasedItems()
    {
        $showId = $this->getRequest()->getParam('id');
        $purchasedItems = $this->eshowPurchasedItemResource->getByShowId($showId);
        $response = [];
        foreach ($purchasedItems as $key => $purchasedItem) {
            $product = $this->productFactory->create();
            $product->load($purchasedItem['item_id']);
            $response[$key] = [
                'sku' => $purchasedItem['sku'],
                'min_qty' => (is_null($purchasedItem['min_qty']) || $purchasedItem['min_qty'] <= 0) ? 0 : $purchasedItem['min_qty'],
                'max_qty' => (is_null($purchasedItem['max_qty']) || $purchasedItem['max_qty'] <= 0) ? 0 : $purchasedItem['max_qty'],
                'price' => (is_null($purchasedItem['custom_price']) || $purchasedItem['custom_price'] <= 0) ? number_format($product->getPrice(), 2) : number_format($purchasedItem['custom_price'], 2),
                'desc' => (is_null($purchasedItem['description']) || trim($purchasedItem['description']) == '') ? trim($product->getName()) : trim($purchasedItem['description'])
            ];
        }
        return $response;
    }
}
