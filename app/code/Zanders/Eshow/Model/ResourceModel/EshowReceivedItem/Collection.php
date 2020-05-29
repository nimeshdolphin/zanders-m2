<?php
/**
 * @category   Zanders
 * @package    Zanders_Eshow
 */

namespace Zanders\Eshow\Model\ResourceModel\EshowReceivedItem;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Zanders\Eshow\Model\EshowReceivedItem as EshowReceivedItemModel;
use Zanders\Eshow\Model\ResourceModel\EshowReceivedItem as EshowReceivedItemResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            EshowReceivedItemModel::class,
            EshowReceivedItemResourceModel::class
        );
    }
}
