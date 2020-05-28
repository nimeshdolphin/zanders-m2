<?php
/**
 * @category   Zanders
 * @package    Zanders_Eshow
 */
 
namespace Zanders\Eshow\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Zanders\Eshow\Model\Eshow;
use Magento\Backend\Model\Session;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;

class InlineEdit extends \Magento\Backend\App\Action
{
    /**
     * @var Eshow
     */
    protected $eshowModel;

    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    public function __construct(
        Action\Context $context,
        JsonFactory $jsonFactory,
        Eshow $eshowModel
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->eshowModel = $eshowModel;
    }

    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData(
                [
                    'messages' => [__('Please correct the data sent.')],
                    'error' => true,
                ]
            );
        }

        foreach (array_keys($postItems) as $eshowId) {
            /** @var Eshow $eshow */
            $eshow = $this->eshowModel->load($eshowId);
            try {
                $eshowData = $postItems[$eshowId];
                $extendedEshowData = $eshow->getData();
                $this->setEshowData($eshow, $extendedEshowData, $eshowData);
                $this->eshowModel->save($eshow);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithExtraChargeId($eshow, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithExtraChargeId($eshow, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithExtraChargeId(
                    $eshow,
                    __('Something went wrong while saving the data.')
                );
                $error = true;
            }
        }

        return $resultJson->setData(
            [
                'messages' => $messages,
                'error' => $error
            ]
        );
    }

    /**
     * Add Eshow name to error message
     *
     * @param Eshow $eshow
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithExtraChargeId(Eshow $eshow, $errorText)
    {
        return '[Eshow ID: ' . $eshow->getId() . '] ' . $errorText;
    }

    /**
     * Set Eshow data
     *
     * @param Eshow $eshow
     * @param array $extendedEshowData
     * @param array $eshowData
     * @return $this
     */
    public function setEshowData(Eshow $eshow, array $extendedEshowData, array $eshowData)
    {
        $eshow->setData(array_merge($eshow->getData(), $extendedEshowData, $eshowData));
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zanders_Eshow::eshow');
    }
}
