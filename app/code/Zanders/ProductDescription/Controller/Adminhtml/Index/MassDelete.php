<?php
/**
 * @category   Zanders
 * @package    Zanders_ProductDescription
 */
namespace Zanders\ProductDescription\Controller\Adminhtml\Index;

use Zanders\ProductDescription\Model\ProductDescription;
use Zanders\ProductDescription\Model\ResourceModel\ProductDescription\CollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;

class MassDelete extends \Magento\Backend\App\Action
{
    protected $filter;
    protected $collectionFactory;
    protected $productDescriptionModel;

    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        ProductDescription $productDescriptionModel
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->productDescriptionModel = $productDescriptionModel;
        parent::__construct($context);
    }

    public function execute()
    {
        $jobData = $this->collectionFactory->create();
        foreach ($jobData as $value) {
            $templateId[]=$value['description_id'];
        }
        $parameterData = $this->getRequest()->getParams('description_id');
        $selectedAppsid = $this->getRequest()->getParams('description_id');
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
        $collection->addFieldToFilter('description_id', ['in'=>$selectedAppsid]);
        $delete = 0;

        foreach ($collection as $item) {
            $this->deleteById($item->getDescriptionId());
            $delete++;
        }
        $this->messageManager->addSuccessMessage(__('A total of %1 Records have been deleted.', $delete));
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Delete Description by id
     * @param  int $id
     * @return void
     * @throws \Exception
     */
    public function deleteById($id)
    {
        $item = $this->productDescriptionModel->load($id);
        $item->delete();
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zanders_ProductDescription::description');
    }
}
