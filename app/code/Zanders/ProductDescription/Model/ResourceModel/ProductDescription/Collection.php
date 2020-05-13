<?php
/**
 * @category   Zanders
 * @package    Zanders_ProductDescription
 */

namespace Zanders\ProductDescription\Model\ResourceModel\ProductDescription;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Zanders\ProductDescription\Model\ProductDescription as ProductDescriptionModel;
use Zanders\ProductDescription\Model\ResourceModel\ProductDescription as ProductDescriptionResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            ProductDescriptionModel::class,
            ProductDescriptionResourceModel::class
        );
    }
}
