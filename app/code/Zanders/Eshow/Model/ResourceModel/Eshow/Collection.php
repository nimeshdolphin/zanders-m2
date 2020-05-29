<?php
/**
 * @category   Zanders
 * @package    Zanders_Eshow
 */

namespace Zanders\Eshow\Model\ResourceModel\Eshow;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Zanders\Eshow\Model\Eshow as EshowModel;
use Zanders\Eshow\Model\ResourceModel\Eshow as EshowResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            EshowModel::class,
            EshowResourceModel::class
        );
    }

    protected function _afterLoad()
    {
        $this->performAfterLoad('zanders_show_specials_manufacturers', 'id', 'show_id');
        return parent::_afterLoad();
    }

    /**
     * Perform operations after collection load
     *
     * @param string $tableName
     * @param string $linkField
     * @param string $refField
     * @return void
     */
    protected function performAfterLoad($tableName, $linkField, $refField)
    {
        $linkedIds = $this->getColumnValues($linkField);
        if (count($linkedIds)) {
            $connection = $this->getConnection();
            $select = $connection->select()->from(['zanders_show_specials_manufacturers' => $this->getTable($tableName)])
                ->where('zanders_show_specials_manufacturers.' . $refField . ' IN (?)', $linkedIds);

            $result = $connection->fetchAll($select);
            if ($result) {
                $manufacturersData = [];
                foreach ($result as $manufacturerData) {
                    $manufacturersData[$manufacturerData[$refField]][] = $manufacturerData['manufacturer_id'];
                }

                foreach ($this as $item) {
                    $linkedId = $item->getData($linkField);

                    if (!isset($manufacturersData[$linkedId])) {
                        continue;
                    }
                    // $item->setData('_first_manufacturer_id', $storeId);
                    // $item->setData('manufacturer_name', $storeCode);
                    $item->setData('manufacturers', $manufacturersData[$linkedId]);

                    /*$storeIdKey = array_search(Store::DEFAULT_STORE_ID, $manufacturersData[$linkedId], true);
                    if ($storeIdKey !== false) {
                        $stores = $this->storeManager->getStores(false, true);
                        $storeId = current($stores)->getId();
                        $storeCode = key($stores);
                    } else {
                        $storeId = current($manufacturersData[$linkedId]);
                        $storeCode = $this->storeManager->getStore($storeId)->getCode();
                    }
                    $item->setData('_first_store_id', $storeId);
                    $item->setData('store_code', $storeCode);
                    $item->setData('store_id', $manufacturersData[$linkedId]);*/
                }
            }
        }
    }
}
