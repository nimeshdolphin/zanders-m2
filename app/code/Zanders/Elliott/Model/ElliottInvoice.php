<?php
/**
 * @category   Zanders
 * @package    Zanders_Elliott
 */

namespace Zanders\Elliott\Model;

use Zanders\Elliott\Model\ResourceModel\ElliottInvoice as ElliottInvoiceResourceModel;
use Magento\Framework\Model\AbstractModel;

class ElliottInvoice extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(ElliottInvoiceResourceModel::class);
    }
}
