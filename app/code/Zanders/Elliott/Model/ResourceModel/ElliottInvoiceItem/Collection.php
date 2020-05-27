<?php
/**
 * @category   Zanders
 * @package    Zanders_Elliott
 */
namespace Zanders\Elliott\Model\ResourceModel\ElliottInvoiceItem;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Zanders\Elliott\Model\ElliottInvoiceItem as ElliottInvoiceItemModel;
use Zanders\Elliott\Model\ResourceModel\ElliottInvoiceItem as ElliottInvoiceItemResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            ElliottInvoiceItemModel::class,
            ElliottInvoiceItemResourceModel::class
        );
    }
}
