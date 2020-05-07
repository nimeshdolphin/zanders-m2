<?php
/**
 * @category   Zanders
 * @package    Zanders_WarningCode
 */

namespace Zanders\WarningCode\Model;

use Magento\Framework\Model\AbstractModel;

class WarningCode extends AbstractModel
{
    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('Zanders\WarningCode\Model\ResourceModel\WarningCode');
    }
}
