<?php
/**
 * @category   Zanders
 * @package    Zanders_Elliott
 */
namespace Zanders\Elliott\Model\ResourceModel\ElliottOrder;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Zanders\Elliott\Model\ElliottOrder as ElliottOrderModel;
use Zanders\Elliott\Model\ResourceModel\ElliottOrder as ElliottOrderResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            ElliottOrderModel::class,
            ElliottOrderResourceModel::class
        );
    }
}
