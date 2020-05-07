<?php
/**
 * @category   Zanders
 * @package    Zanders_CategoryCode
 */

namespace Zanders\CategoryCode\Model;

use Magento\Framework\Model\AbstractModel;

class CategoryCode extends AbstractModel
{
    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('Zanders\CategoryCode\Model\ResourceModel\CategoryCode');
    }
}