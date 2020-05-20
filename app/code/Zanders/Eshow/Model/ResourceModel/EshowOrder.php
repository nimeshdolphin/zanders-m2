<?php
/**
 * @category   Zanders
 * @package    Zanders_Eshow
 */

namespace Zanders\Eshow\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class EshowOrder extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('zanders_show_orders', 'id');
    }

    /**
     * Get Order data by id
     *
     * @param int $id
     * @return array
     */
    public function getOrderHtmlById($id)
    {
        $connection = $this->getConnection();

        $select = $connection->select()
            ->from($this->getConnection()->getTableName('zanders_show_orders'), ['save'])
            ->where('id = :id');
        $bind = [':id' => (int)$id];

        return $connection->fetchOne($select, $bind);
    }
}
