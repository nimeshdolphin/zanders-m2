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

    /**
     * Get WarningCode data by warningCode
     *
     * @param string $warningCode
     * @return array
     */
    public function getByCode($warningCode)
    {
        $connection = $this->getConnection();

        $select = $connection->select()
            ->from($this->getConnection()->getTableName('zanders_warningcode'), '*')
            ->where('warning_code = :warningCode');
        $bind = [':warningCode' => (string)$warningCode];

        return $connection->fetchRow($select, $bind);
    }
}