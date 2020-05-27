<?php
/**
 * @category   Zanders
 * @package    Zanders_FlatRate
 */

namespace Zanders\FlatRate\Model;

use Zanders\FlatRate\Model\ResourceModel\FlatRate as FlatRateResourceModel;
use Magento\Framework\Model\AbstractModel;

class FlatRate extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(FlatRateResourceModel::class);
    }
}
