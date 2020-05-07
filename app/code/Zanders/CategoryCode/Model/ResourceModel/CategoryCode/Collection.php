<?php
/**
 * @category   Zanders
 * @package    Zanders_CategoryCode
 */

namespace Zanders\CategoryCode\Model\ResourceModel\CategoryCode;
 
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'categorycode_id';
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(
            'Zanders\CategoryCode\Model\CategoryCode',
            'Zanders\CategoryCode\Model\ResourceModel\CategoryCode'
        );
    }
}