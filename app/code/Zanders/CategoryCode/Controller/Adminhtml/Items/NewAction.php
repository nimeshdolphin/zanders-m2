<?php
/**
 * @category   Zanders
 * @package    Zanders_CategoryCode
 */

namespace Zanders\CategoryCode\Controller\Adminhtml\Items;

class NewAction extends \Zanders\CategoryCode\Controller\Adminhtml\Items
{

    public function execute()
    {
        $this->_forward('edit');
    }
}
