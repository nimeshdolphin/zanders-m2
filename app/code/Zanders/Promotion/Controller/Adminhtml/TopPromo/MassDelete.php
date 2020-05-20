<?php
/**
 * @category   Zanders
 * @package    Zanders_Promotion
 */
namespace Zanders\Promotion\Controller\Adminhtml\TopPromo;

use Zanders\Promotion\Model\TopPromo;
use Zanders\Promotion\Model\ResourceModel\TopPromo\CollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;

class MassDelete extends \Magento\Backend\App\Action
{
    protected $filter;
    protected $collectionFactory;
    protected $topPromoModel;

    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        TopPromo $topPromoModel
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->topPromoModel = $topPromoModel;
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
     * Delete TopPromo by id
     * @param  int $id
     * @return void
     * @throws \Exception
     */
    public function deleteById($id)
    {
        $item = $this->topPromoModel->load($id);
        $item->delete();
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zanders_Promotion::top_promo');
    }
}
