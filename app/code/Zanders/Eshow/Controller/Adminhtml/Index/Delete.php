<?php
/**
 * @category   Zanders
 * @package    Zanders_Eshow
 */
namespace Zanders\Eshow\Controller\Adminhtml\Index;

use Zanders\Eshow\Model\Eshow;
use Magento\Backend\App\Action;

class Delete extends Action
{
    protected $eshowModel;

    /**
     * @param Action\Context $context
     * @param Eshow $model
     */
    public function __construct(
        Action\Context $context,
        Eshow $model
    ) {
        parent::__construct($context);
        $this->eshowModel = $model;
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
                $model = $this->eshowModel;
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
        return $this->_authorization->isAllowed('Zanders_Eshow::eshow');
    }
}
