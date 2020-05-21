<?php
/**
 * @category   Zanders
 * @package    Zanders_Elliott
 */

namespace Zanders\Elliott\Controller\Elliott;

class Test extends \Magento\Framework\App\Action\Action
{
    public function execute()
    {
        $customer = new \Zanders_Elliott_Model_Elliott_Customer();
        var_dump($customer->getTermsList());

        echo 'TEST';
        exit;
    }
}
