<?php
/**
 * @category   Zanders
 * @package    Zanders_Promotion
 */
namespace Zanders\Promotion\Controller\Adminhtml\TopPromo;

use Zanders\Promotion\Model\TopPromo;
use Magento\Backend\App\Action;

class Delete extends Action
{
    protected $topPromoModel;

    /**
     * @param Action\Context $context
     * @param TopPromo $model
     */
    public function __construct(
        Action\Context $context,
        TopPromo $model
    ) {
        parent::__construct($context);
        $this->topPromoModel = $model;
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('toppromo_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $model = $this->topPromoModel;
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
        return $this->_authorization->isAllowed('Zanders_Promotion::top_promo');
    }
}
