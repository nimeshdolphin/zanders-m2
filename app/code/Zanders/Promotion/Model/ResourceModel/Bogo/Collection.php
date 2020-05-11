<?php
/**
 * @category   Zanders
 * @package    Zanders_Promotion
 */

namespace Zanders\Promotion\Model\ResourceModel\Bogo;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Zanders\Promotion\Model\Bogo as BogoModel;
use Zanders\Promotion\Model\ResourceModel\Bogo as BogoResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            BogoModel::class,
            BogoResourceModel::class
        );
    }
}
