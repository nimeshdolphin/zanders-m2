<?php
/**
 * @category   Zanders
 * @package    Zanders_WarningCode
 */

namespace Zanders\WarningCode\Controller\Adminhtml\Items;

class Index extends \Zanders\WarningCode\Controller\Adminhtml\Items
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
        $resultPage->setActiveMenu('Zanders_WarningCode::warning_code');
        $resultPage->getConfig()->getTitle()->prepend(__('Warning Codes'));
        $resultPage->addBreadcrumb(__('Warning Codes'), __('Warning Codes'));
        return $resultPage;
    }
}