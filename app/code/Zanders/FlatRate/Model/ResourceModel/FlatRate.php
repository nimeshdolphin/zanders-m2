<?php
/**
 * @category   Zanders
 * @package    Zanders_FlatRate
 */

namespace Zanders\FlatRate\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class FlatRate extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('zanders_flatrate', 'id');
    }
}
