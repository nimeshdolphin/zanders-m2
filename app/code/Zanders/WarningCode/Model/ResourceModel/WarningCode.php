<?php
/**
 * @category   Zanders
 * @package    Zanders_WarningCode
 */

namespace Zanders\WarningCode\Model\ResourceModel;

class WarningCode extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('zanders_warningcode', 'warningcode_id');
    }
}