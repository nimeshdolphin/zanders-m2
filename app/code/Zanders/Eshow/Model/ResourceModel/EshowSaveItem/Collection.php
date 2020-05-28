<?php
/**
 * @category   Zanders
 * @package    Zanders_Eshow
 */

namespace Zanders\Eshow\Model\ResourceModel\EshowSaveItem;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Zanders\Eshow\Model\EshowSaveItem as EshowSaveModel;
use Zanders\Eshow\Model\ResourceModel\EshowSaveItem as EshowSaveResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            EshowSaveModel::class,
            EshowSaveResourceModel::class
        );
    }
}
