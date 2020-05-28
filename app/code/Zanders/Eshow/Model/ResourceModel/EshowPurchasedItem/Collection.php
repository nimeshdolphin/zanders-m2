<?php
/**
 * @category   Zanders
 * @package    Zanders_Eshow
 */

namespace Zanders\Eshow\Model\ResourceModel\EshowPurchasedItem;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Zanders\Eshow\Model\EshowPurchasedItem as EshowPurchasedItemModel;
use Zanders\Eshow\Model\ResourceModel\EshowPurchasedItem as EshowPurchasedItemResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            EshowPurchasedItemModel::class,
            EshowPurchasedItemResourceModel::class
        );
    }
}
