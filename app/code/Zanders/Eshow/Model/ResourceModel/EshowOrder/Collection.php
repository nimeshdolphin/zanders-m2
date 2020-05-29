<?php
/**
 * @category   Zanders
 * @package    Zanders_Eshow
 */

namespace Zanders\Eshow\Model\ResourceModel\EshowOrder;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Zanders\Eshow\Model\EshowOrder as EshowOrderModel;
use Zanders\Eshow\Model\ResourceModel\EshowOrder as EshowOrderResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            EshowOrderModel::class,
            EshowOrderResourceModel::class
        );
    }
}
