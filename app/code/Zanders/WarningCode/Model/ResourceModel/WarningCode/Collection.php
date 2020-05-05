<?php
/**
 * @category   Zanders
 * @package    Zanders_WarningCode
 */

namespace Zanders\WarningCode\Model\ResourceModel\WarningCode;
 
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'warningcode_id';
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(
            'Zanders\WarningCode\Model\WarningCode',
            'Zanders\WarningCode\Model\ResourceModel\WarningCode'
        );
    }
}