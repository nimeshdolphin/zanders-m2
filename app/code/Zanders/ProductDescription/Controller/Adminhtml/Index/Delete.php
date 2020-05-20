<?php
/**
 * @category   Zanders
 * @package    Zanders_ProductDescription
 */
namespace Zanders\ProductDescription\Controller\Adminhtml\Index;

use Zanders\ProductDescription\Model\ProductDescription;
use Magento\Backend\App\Action;

class Delete extends Action
{
    protected $productDescriptionModel;

    /**
     * @param Action\Context $context
     * @param ProductDescription $model
     */
    public function __construct(
        Action\Context $context,
        ProductDescription $model
    ) {
        parent::__construct($context);
        $this->productDescriptionModel = $model;
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $model = $this->productDescriptionModel;
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccessMessage(__('Record deleted'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('Record does not exist'));
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zanders_ProductDescription::description');
    }
}
