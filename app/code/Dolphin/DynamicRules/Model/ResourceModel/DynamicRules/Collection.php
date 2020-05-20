<?php declare(strict_types=1);


namespace Dolphin\DynamicRules\Model\ResourceModel\DynamicRules;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Dolphin\DynamicRules\Model\DynamicRules::class,
            \Dolphin\DynamicRules\Model\ResourceModel\DynamicRules::class
        );
    }
}
