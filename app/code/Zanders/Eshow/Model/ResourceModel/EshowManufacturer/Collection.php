<?php
/**
 * @category   Zanders
 * @package    Zanders_Eshow
 */

namespace Zanders\Eshow\Model\ResourceModel\EshowManufacturer;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Zanders\Eshow\Model\EshowManufacturer as EshowManufacturerModel;
use Zanders\Eshow\Model\ResourceModel\EshowManufacturer as EshowManufacturerResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            EshowManufacturerModel::class,
            EshowManufacturerResourceModel::class
        );
    }
}
