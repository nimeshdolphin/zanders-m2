<?php
/**
 * @category   Zanders
 * @package    Zanders_Eshow
 */

namespace Zanders\Eshow\Block\Adminhtml\Index;

use Zanders\Eshow\Model\ResourceModel\EshowReceivedItem as EshowReceivedItemResource;
use Magento\Catalog\Model\ProductFactory;

class ReceivedItem extends \Magento\Backend\Block\Template
{
    protected $_template = 'Zanders_Eshow::receive_items_table.phtml';

    /**
     * @var EshowReceivedItemResource $eshowReceivedItemResource
     */
    protected $eshowReceivedItemResource;

    protected $productFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param ProductFactory $productFactory
     * @param EshowReceivedItemResource $eshowReceivedItemResource
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        ProductFactory $productFactory,
        EshowReceivedItemResource $eshowReceivedItemResource
    ) {
        $this->productFactory = $productFactory;
        $this->eshowReceivedItemResource = $eshowReceivedItemResource;
        parent::__construct($context);
    }

    public function getReceivedItems()
    {
        $showId = $this->getRequest()->getParam('id');
        $receivedItems = $this->eshowReceivedItemResource->getByShowId($showId);
        $response = [];
        foreach ($receivedItems as $key => $receivedItem) {
            $product = $this->productFactory->create();
            $product->load($receivedItem['item_id']);
            $response[$key] = [
                'sku' => $receivedItem['sku'],
                'min_qty' => (is_null($receivedItem['min_qty']) || $receivedItem['min_qty'] <= 0) ? 0 : $receivedItem['min_qty'],
                'max_qty' => (is_null($receivedItem['max_qty']) || $receivedItem['max_qty'] <= 0) ? 0 : $receivedItem['max_qty'],
                'price' => (is_null($receivedItem['custom_price']) || $receivedItem['custom_price'] <= 0) ? number_format($product->getPrice(), 2) : number_format($receivedItem['custom_price'], 2),
                'desc' => (is_null($receivedItem['description']) || trim($receivedItem['description']) == '') ? trim($product->getName()) : trim($receivedItem['description'])
            ];
        }
        return $response;
    }
}
