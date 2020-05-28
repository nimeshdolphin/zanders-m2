<?php
/**
 * @category   Zanders
 * @package    Zanders_Eshow
 */

namespace Zanders\Eshow\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class EshowReceivedItem extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('zanders_show_specials_receive', 'id');
    }

    /**
     * Get Received Items data by sku
     *
     * @param int $showId
     * @return array
     */
    public function getByShowId($showId)
    {
        $connection = $this->getConnection();

        $select = $connection->select()
            ->from($this->getConnection()->getTableName('zanders_show_specials_receive'), '*')
            ->where('show_id = :show_id');
        $bind = [':show_id' => (int)$showId];

        return $connection->fetchAssoc($select, $bind);
    }

    /**
     * Get Description data by sku
     *
     * @param int $showId
     * @return array
     */
    public function getIdSkuPairs($showId)
    {
        $connection = $this->getConnection();

        $select = $connection->select()
            ->from($this->getConnection()->getTableName('zanders_show_specials_receive'), ['id','sku'])
            ->where('show_id = :show_id');
        $bind = [':show_id' => (int)$showId];

        return $connection->fetchPairs($select, $bind);
    }

    /**
     * Delete Manufacturer data
     *
     * @param string $showId
     * @param array $manufacturerIds
     * @return int
     */
    public function deleteBySkus($showId, $skus)
    {
        $where = ['show_id = ?' => $showId, 'sku IN (?)' => $skus];
        return $this->getConnection()->delete($this->getTable('zanders_show_specials_receive'), $where);
    }
}
