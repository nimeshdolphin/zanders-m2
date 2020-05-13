<?php
/**
 * @category   Zanders
 * @package    Zanders_ProductDescription
 */

namespace Zanders\ProductDescription\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ProductDescription extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('zanders_description', 'description_id');
    }

    /**
     * Get Description data by sku
     *
     * @param string $sku
     * @return array
     */
    public function getDescriptionBySku($sku)
    {
        $connection = $this->getConnection();

        $select = $connection->select()
            ->from($this->getConnection()->getTableName('zanders_description'), 'html')
            ->where('sku = :sku');
        $bind = [':sku' => (string)$sku];

        return $connection->fetchRow($select, $bind);
    }
}
