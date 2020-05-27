<?php
/**
 * @category   Zanders
 * @package    Zanders_FlatRate
 */

namespace Zanders\FlatRate\Model\ResourceModel\FlatRate;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Zanders\FlatRate\Model\FlatRate as FlatRateModel;
use Zanders\FlatRate\Model\ResourceModel\FlatRate as FlatRateResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            FlatRateModel::class,
            FlatRateResourceModel::class
        );
    }
}
