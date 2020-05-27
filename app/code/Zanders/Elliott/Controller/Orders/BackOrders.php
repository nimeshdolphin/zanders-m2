<?php
/**
 * @category   Zanders
 * @package    Zanders_Elliott
 */

namespace Zanders\Elliott\Controller\Orders;

use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;

class BackOrders extends \Zanders\Elliott\Controller\AbstractAccount implements HttpGetActionInterface
{
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }
}
