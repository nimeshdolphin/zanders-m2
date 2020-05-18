<?php declare(strict_types=1);


namespace Zanders\PromotedManufacturer\Controller\Adminhtml\PromotedManufacturer;


class Delete extends \Zanders\PromotedManufacturer\Controller\Adminhtml\PromotedManufacturer
{

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create(\Zanders\PromotedManufacturer\Model\PromotedManufacturer::class);
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Promotedmanufacturer.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Promotedmanufacturer to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}

