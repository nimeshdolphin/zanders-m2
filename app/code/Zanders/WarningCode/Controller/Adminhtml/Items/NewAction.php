<?php
/**
 * @category   Zanders
 * @package    Zanders_WarningCode
 */

namespace Zanders\WarningCode\Controller\Adminhtml\Items;

class NewAction extends \Zanders\WarningCode\Controller\Adminhtml\Items
{

    public function execute()
    {
        $this->_forward('edit');
    }
}
