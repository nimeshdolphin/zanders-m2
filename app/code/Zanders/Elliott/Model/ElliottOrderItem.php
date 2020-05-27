<?php
/**
 * @category   Zanders
 * @package    Zanders_Elliott
 */

namespace Zanders\Elliott\Model;

use Zanders\Elliott\Model\ResourceModel\ElliottOrderItem as ElliottOrderItemResourceModel;
use Magento\Framework\Model\AbstractModel;

class ElliottOrderItem extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(ElliottOrderItemResourceModel::class);
    }
}
