<?php
/**
 * @category   Zanders
 * @package    Zanders_CategoryCode
 */

namespace Zanders\CategoryCode\Model\ResourceModel;

class CategoryCode extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('zanders_categorycode', 'categorycode_id');   //here "zanders_categorycode" is table name and "categorycode_id" is the primary key of custom table
    }
}