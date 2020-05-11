<?php
/**
 * @category   Zanders
 * @package    Zanders_Promotion
 */
namespace Zanders\Promotion\Controller\Adminhtml\Bogo;

use Magento\Backend\App\Action;
use Zanders\Promotion\Model\Bogo;
use Magento\Backend\Model\Session;

class Save extends \Magento\Backend\App\Action
{
    protected $bogoModel;
    protected $adminsession;

    public function __construct(
        Action\Context $context,
        Bogo $bogoModel,
        Session $adminsession
    ) {
        parent::__construct($context);
        $this->bogoModel = $bogoModel;
        $this->adminsession = $adminsession;
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $bogo_id = $this->getRequest()->getParam('bogo_id');

            if ($bogo_id) {
                $this->bogoModel->load($bogo_id);
            }

            $this->bogoModel->setData($data);

            try {
                $this->bogoModel->save();
                $this->messageManager->addSuccessMessage(__('The data has been saved.'));
                $this->adminsession->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    if ($this->getRequest()->getParam('back') == 'add') {
                        return $resultRedirect->setPath('*/*/add');
                    } else {
                        return $resultRedirect->setPath(
                            '*/*/edit',
                            [
                                'bogo_id' => $this->bogoModel->getBogoId(),
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
            return $resultRedirect->setPath('*/*/edit', ['bogo_id' => $this->getRequest()->getParam('bogo_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zanders_Promotion::bogo');
    }
}
