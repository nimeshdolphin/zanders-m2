<?php
/**
 * @category   Zanders
 * @package    Zanders_Promotion
 */

namespace Zanders\Promotion\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class TopPromo extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('zanders_toppromo', 'toppromo_id');
    }
}