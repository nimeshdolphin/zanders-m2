<?php
/**
 * @category   Zanders
 * @package    Zanders_Elliott
 */

namespace Zanders\Elliott\Model;

use Zanders\Elliott\Model\ResourceModel\ElliottOrder as ElliottOrderResourceModel;
use Magento\Framework\Model\AbstractModel;

class ElliottOrder extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(ElliottOrderResourceModel::class);
    }
}
