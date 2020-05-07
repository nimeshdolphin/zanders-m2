<?php
/**
 * @category   Zanders
 * @package    Zanders_CategoryCode
 */

namespace Zanders\CategoryCode\Controller\Adminhtml\Items;

class Index extends \Zanders\CategoryCode\Controller\Adminhtml\Items
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
        $resultPage->setActiveMenu('Zanders_CategoryCode::category_code');
        $resultPage->getConfig()->getTitle()->prepend(__('Category Codes'));
        $resultPage->addBreadcrumb(__('Category Codes'), __('Category Codes'));
        return $resultPage;
    }
}