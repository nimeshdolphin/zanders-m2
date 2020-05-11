<?php
/**
 * @category   Zanders
 * @package    Zanders_Promotion
 */
namespace Zanders\Promotion\Controller\Adminhtml\Bogo;

use Zanders\Promotion\Model\Bogo;
use Zanders\Promotion\Model\ResourceModel\Bogo\CollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;

class MassDelete extends \Magento\Backend\App\Action
{
    protected $filter;
    protected $collectionFactory;
    protected $bogoModel;

    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        Bogo $bogoModel
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->bogoModel = $bogoModel;
        parent::__construct($context);
    }

    public function execute()
    {
        $jobData = $this->collectionFactory->create();
        foreach ($jobData as $value) {
            $templateId[]=$value['bogo_id'];
        }
        $parameterData = $this->getRequest()->getParams('bogo_id');
        $selectedAppsid = $this->getRequest()->getParams('bogo_id');
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
        $collection->addFieldToFilter('bogo_id', ['in'=>$selectedAppsid]);
        $delete = 0;

        foreach ($collection as $item) {
            $this->deleteById($item->getBogoId());
            $delete++;
        }
        $this->messageManager->addSuccessMessage(__('A total of %1 Records have been deleted.', $delete));
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Delete bogo by id
     * @param  int $id
     * @return void
     * @throws \Exception
     */
    public function deleteById($id)
    {
        $item = $this->bogoModel->load($id);
        $item->delete();
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zanders_Promotion::bogo');
    }
}
