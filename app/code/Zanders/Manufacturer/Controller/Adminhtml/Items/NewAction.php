<?php
/**
 * @category   Zanders
 * @package    Zanders_Manufacturer
 */

namespace Zanders\Manufacturer\Controller\Adminhtml\Items;

class NewAction extends \Zanders\Manufacturer\Controller\Adminhtml\Items
{

    public function execute()
    {
        $this->_forward('edit');
    }
}
