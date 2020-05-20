<?php
/**
 * @category   Zanders
 * @package    Zanders_Promotion
 */

namespace Zanders\Promotion\Model\ResourceModel;

use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Bogo extends AbstractDb
{
    /**
     * @var ProductFactory
     */
    protected $productFactory;

    protected function _construct()
    {
        $this->_init('zanders_bogo', 'id');
    }

    /**
     * Constructor
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param ProductFactory $productFactory
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        ProductFactory $productFactory,
        $connectionName = null
    )
    {
        parent::__construct($context, $connectionName);
        $this->productFactory = $productFactory;
    }

    /**
     * Perform actions before object save
     *
     * @param \Magento\Framework\Model\AbstractModel|\Magento\Framework\DataObject $object
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws LocalizedException
     */
    protected function _beforeSave(AbstractModel $model)
    {
        $buyOneId = $this->productFactory->create()->getIdBySku($model->getData('buy_one_sku'));
        if (!$buyOneId)
            throw new LocalizedException(new \Magento\Framework\Phrase(sprintf('Buy One Sku "%s" not found.', $model->getData('buy_one_sku'))));

        $getOneId = $this->productFactory->create()->getIdBySku($model->getData('get_one_sku'));
        if (!$getOneId)
            throw new LocalizedException(new \Magento\Framework\Phrase(sprintf('Get One Sku "%s" not found.', $model->getData('get_one_sku'))));

        $model->setData('buy_one_id', $buyOneId);
        $model->setData('get_one_id', $getOneId);
        return parent::_beforeSave($model);
    }
}
