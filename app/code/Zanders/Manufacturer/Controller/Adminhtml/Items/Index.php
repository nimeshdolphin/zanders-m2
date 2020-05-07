<?php
/**
 * @category   Zanders
 * @package    Zanders_Manufacturer
 */

namespace Zanders\Manufacturer\Controller\Adminhtml\Items;

class Index extends \Zanders\Manufacturer\Controller\Adminhtml\Items
{
    /**
     * Items list.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Zanders_Manufacturer::manufacturer');
        $resultPage->getConfig()->getTitle()->prepend(__('Manufacturers'));
        $resultPage->addBreadcrumb(__('Manufacturers'), __('Manufacturers'));
        return $resultPage;
    }
}