<?php declare(strict_types=1);


namespace Dolphin\DynamicRules\Model\ResourceModel;


class DynamicRules extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('dolphin_dynamicrules_dynamicrules', 'id');
    }
}

