<?php
/**
 * @category   Zanders
 * @package    Zanders_Promotion
 */
namespace Zanders\Promotion\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Zanders\Promotion\Model\Promotion;
use Magento\Backend\Model\Session;

class Save extends \Magento\Backend\App\Action
{
    protected $promotionModel;
    protected $adminsession;

    public function __construct(
        Action\Context $context,
        Promotion $promotionModel,
        Session $adminsession
    ) {
        parent::__construct($context);
        $this->promotionModel = $promotionModel;
        $this->adminsession = $adminsession;
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $promotion_id = $this->getRequest()->getParam('promotion_id');

            if ($promotion_id) {
                $this->promotionModel->load($promotion_id);
            }

            $this->promotionModel->setData($data);

            try {
                $this->promotionModel->save();
                $this->messageManager->addSuccessMessage(__('The data has been saved.'));
                $this->adminsession->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    if ($this->getRequest()->getParam('back') == 'add') {
                        return $resultRedirect->setPath('*/*/add');
                    } else {
                        return $resultRedirect->setPath(
                            '*/*/edit',
                            [
                                'promotion_id' => $this->promotionModel->getPromotionId(),
                                '_current' => true
                            ]
                        );
                    }
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the data.'));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['promotion_id' => $this->getRequest()->getParam('promotion_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zanders_Promotion::promotion');
    }
}
