<?php
/**
 * @category   Zanders
 * @package    Zanders_Promotion
 */

namespace Zanders\Promotion\Controller\Index;

class Promotional extends \Magento\Framework\App\Action\Action
{
	public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }
}
