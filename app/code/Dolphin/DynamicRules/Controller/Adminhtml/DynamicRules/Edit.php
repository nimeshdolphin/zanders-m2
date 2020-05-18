<?php declare(strict_types=1);


namespace Dolphin\DynamicRules\Controller\Adminhtml\DynamicRules;


class Edit extends \Dolphin\DynamicRules\Controller\Adminhtml\DynamicRules
{

    protected $resultPageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create(\Dolphin\DynamicRules\Model\DynamicRules::class);
        
        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Dynamicrules no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->_coreRegistry->register('dolphin_dynamicrules_dynamicrules', $model);
        
        // 3. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Dynamicrules') : __('New Dynamicrules'),
            $id ? __('Edit Dynamicrules') : __('New Dynamicrules')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Dynamicruless'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? __('Edit Dynamicrules %1', $model->getId()) : __('New Dynamicrules'));
        return $resultPage;
    }
}

