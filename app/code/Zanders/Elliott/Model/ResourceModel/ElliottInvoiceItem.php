<?php
/**
 * @category   Zanders
 * @package    Zanders_Elliott
 */
namespace Zanders\Elliott\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ElliottInvoiceItem extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('eliinvoice_items', 'id');
    }
}
