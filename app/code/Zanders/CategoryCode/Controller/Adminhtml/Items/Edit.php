<?php
/**
 * @category   Zanders
 * @package    Zanders_CategoryCode
 */

namespace Zanders\CategoryCode\Controller\Adminhtml\Items;

class Edit extends \Zanders\CategoryCode\Controller\Adminhtml\Items
{

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        
        $model = $this->_objectManager->create('Zanders\CategoryCode\Model\CategoryCode');

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This item no longer exists.'));
                $this->_redirect('zanders_categorycode/*');
                return;
            }
        }
        // set entered data if was error when we do save
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        $this->_coreRegistry->register('current_zanders_categorycode_items', $model);
        $this->_initAction();

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $title = $model->getId() ? __('Edit Category Code "%1"', $model->getData('code')) : __('Add Category Code');
        $resultPage->getConfig()->getTitle()->prepend($title);

        $this->_view->getLayout()->getBlock('items_items_edit');
        $this->_view->renderLayout();
    }
}
