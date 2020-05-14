<?php
/**
 * @category   Zanders
 * @package    Zanders_Eshow
 */

namespace Zanders\Eshow\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Eshow extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('zanders_show_specials', 'id');
    }
}
