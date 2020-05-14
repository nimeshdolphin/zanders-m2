<?php
/**
 * @category   Zanders
 * @package    Zanders_Eshow
 */

namespace Zanders\Eshow\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class EshowManufacturer extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('zanders_show_specials_manufacturers', 'id');
    }

    /**
     * Get Description data by sku
     *
     * @param int $showId
     * @return array
     */
    public function getByShowId($showId)
    {
        $connection = $this->getConnection();

        $select = $connection->select()
            ->from($this->getConnection()->getTableName('zanders_show_specials_manufacturers'), ['id','manufacturer_id'])
            ->where('show_id = :show_id');
        $bind = [':show_id' => (int)$showId];

        return $connection->fetchPairs($select, $bind);
    }

    /**
     * Get Description data by sku
     *
     * @param string $showId
     * @param array $manufacturerIds
     * @return int
     */
    public function deleteByManufacturerIds($showId, $manufacturerIds)
    {
        $where = ['show_id = ?' => $showId, 'manufacturer_id IN (?)' => $manufacturerIds];
        return $this->getConnection()->delete($this->getTable('zanders_show_specials_manufacturers'), $where);
    }
}
