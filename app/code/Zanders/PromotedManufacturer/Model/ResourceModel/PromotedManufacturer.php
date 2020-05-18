<?php declare(strict_types=1);


namespace Zanders\PromotedManufacturer\Model\ResourceModel;


class PromotedManufacturer extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('zanders_promotedmanufacturer_promotedmanufacturer', 'id');
    }
}

