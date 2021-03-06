<?php
/**
 * @category   Zanders
 * @package    Zanders_Eshow
 */
namespace Zanders\Eshow\Controller\Adminhtml\Index;

use Zanders\Eshow\Model\Eshow;
use Zanders\Eshow\Model\ResourceModel\Eshow\CollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;

class MassDelete extends \Magento\Backend\App\Action
{
    protected $filter;
    protected $collectionFactory;
    protected $eshowModel;

    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        Eshow $eshowModel
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->eshowModel = $eshowModel;
        parent::__construct($context);
    }

    public function execute()
    {
        $jobData = $this->collectionFactory->create();
        foreach ($jobData as $value) {
            $templateId[]=$value['id'];
        }
        $parameterData = $this->getRequest()->getParams('id');
        $selectedAppsid = $this->getRequest()->getParams('id');
        if (array_key_exists("selected", $parameterData)) {
            $selectedAppsid = $parameterData['selected'];
        }
        if (array_key_exists("excluded", $parameterData)) {
            if ($parameterData['excluded'] == 'false') {
                $selectedAppsid = $templateId;
            } else {
                $selectedAppsid = array_diff($templateId, $parameterData['excluded']);
            }
        }
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('id', ['in'=>$selectedAppsid]);
        $delete = 0;

        foreach ($collection as $item) {
            $this->deleteById($item->getId());
            $delete++;
        }
        $this->messageManager->addSuccessMessage(__('A total of %1 Records have been deleted.', $delete));
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Delete eshow by id
     * @param  int $id
     * @return void
     * @throws \Exception
     */
    public function deleteById($id)
    {
        $item = $this->eshowModel->load($id);
        $item->delete();
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zanders_Eshow::eshow');
    }
}
