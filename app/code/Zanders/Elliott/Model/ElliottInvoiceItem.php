<?php
/**
 * @category   Zanders
 * @package    Zanders_Elliott
 */

namespace Zanders\Elliott\Model;

use Zanders\Elliott\Model\ResourceModel\ElliottInvoiceItem as ElliottInvoiceItemResourceModel;
use Magento\Framework\Model\AbstractModel;

class ElliottInvoiceItem extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(ElliottInvoiceItemResourceModel::class);
    }
}
