<?php
/**
 * @category   Zanders
 * @package    Zanders_Eshow
 */

namespace Zanders\Eshow\Block\Adminhtml\Order;

use Zanders\Eshow\Model\ResourceModel\EshowOrder as EshowOrderResource;
use Magento\Catalog\Model\ProductFactory;

class View extends \Magento\Backend\Block\Template
{
    protected $_template = 'Zanders_Eshow::eshow_order.phtml';

    /**
     * @var EshowOrderResource $eshowOrderResource
     */
    protected $eshowOrderResource;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param EshowOrderResource $eshowOrderResource
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        EshowOrderResource $eshowOrderResource
    ) {
        $this->eshowOrderResource = $eshowOrderResource;
        parent::__construct($context);
    }

    public function getOrderHtml()
    {
        $id = $this->getRequest()->getParam('id');
        return $this->eshowOrderResource->getOrderHtmlById($id);
    }
}
