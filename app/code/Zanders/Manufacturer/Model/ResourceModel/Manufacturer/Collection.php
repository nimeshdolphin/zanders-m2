<?php
/**
 * @category   Zanders
 * @package    Zanders_Manufacturer
 */

namespace Zanders\Manufacturer\Model\ResourceModel\Manufacturer;
 
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(
            'Zanders\Manufacturer\Model\Manufacturer',
            'Zanders\Manufacturer\Model\ResourceModel\Manufacturer'
        );
    }
}