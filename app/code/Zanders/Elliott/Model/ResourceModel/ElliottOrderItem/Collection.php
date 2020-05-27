<?php
/**
 * @category   Zanders
 * @package    Zanders_Elliott
 */
namespace Zanders\Elliott\Model\ResourceModel\ElliottOrderItem;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Zanders\Elliott\Model\ElliottOrderItem as ElliottOrderItemModel;
use Zanders\Elliott\Model\ResourceModel\ElliottOrderItem as ElliottOrderItemResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            ElliottOrderItemModel::class,
            ElliottOrderItemResourceModel::class
        );
    }
}
