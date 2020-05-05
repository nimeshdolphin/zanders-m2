<?php
/**
 * @category   Zanders
 * @package    Zanders_WarningCode
 */

namespace Zanders\WarningCode\Controller\Adminhtml\Items;

class Edit extends \Zanders\WarningCode\Controller\Adminhtml\Items
{

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        
        $model = $this->_objectManager->create('Zanders\WarningCode\Model\WarningCode');

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This item no longer exists.'));
                $this->_redirect('zanders_warningcode/*');
                return;
            }
        }
        // set entered data if was error when we do save
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $this->_coreRegistry->register('current_zanders_warningcode_items', $model);
        $this->_initAction();

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $title = $model->getId() ? __('Edit Warning Code "%1"', $model->getData('warning_code')) : __('Add Warning Code');
        $resultPage->getConfig()->getTitle()->prepend($title);

        $this->_view->getLayout()->getBlock('items_items_edit');
        $this->_view->renderLayout();
    }
}
