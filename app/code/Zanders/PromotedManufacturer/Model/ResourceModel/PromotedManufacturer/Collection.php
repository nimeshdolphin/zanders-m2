<?php declare(strict_types=1);


namespace Zanders\PromotedManufacturer\Model\ResourceModel\PromotedManufacturer;


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
            \Zanders\PromotedManufacturer\Model\PromotedManufacturer::class,
            \Zanders\PromotedManufacturer\Model\ResourceModel\PromotedManufacturer::class
        );
    }
}

