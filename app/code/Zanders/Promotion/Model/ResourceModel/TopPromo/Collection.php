<?php
/**
 * @category   Zanders
 * @package    Zanders_Promotion
 */

namespace Zanders\Promotion\Model\ResourceModel\TopPromo;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Zanders\Promotion\Model\TopPromo as TopPromoModel;
use Zanders\Promotion\Model\ResourceModel\TopPromo as TopPromoResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            TopPromoModel::class,
            TopPromoResourceModel::class
        );
    }
}
