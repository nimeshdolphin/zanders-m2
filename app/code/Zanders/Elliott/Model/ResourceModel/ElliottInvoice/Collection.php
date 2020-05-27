<?php
/**
 * @category   Zanders
 * @package    Zanders_Elliott
 */
namespace Zanders\Elliott\Model\ResourceModel\ElliottInvoice;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Zanders\Elliott\Model\ElliottInvoice as ElliottInvoiceModel;
use Zanders\Elliott\Model\ResourceModel\ElliottInvoice as ElliottInvoiceResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            ElliottInvoiceModel::class,
            ElliottInvoiceResourceModel::class
        );
    }
}
