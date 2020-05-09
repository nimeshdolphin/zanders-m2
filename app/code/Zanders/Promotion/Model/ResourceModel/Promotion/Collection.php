<?php
/**
 * @category   Zanders
 * @package    Zanders_Promotion
 */

namespace Zanders\Promotion\Model\ResourceModel\Promotion;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Zanders\Promotion\Model\Promotion as PromotionModel;
use Zanders\Promotion\Model\ResourceModel\Promotion as PromotionResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            PromotionModel::class,
            PromotionResourceModel::class
        );
    }
}
