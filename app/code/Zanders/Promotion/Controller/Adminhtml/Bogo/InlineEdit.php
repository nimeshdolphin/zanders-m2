<?php
/**
 * @category   Zanders
 * @package    Zanders_Promotion
 */
 
namespace Zanders\Promotion\Controller\Adminhtml\Bogo;

use Magento\Backend\App\Action;
use Zanders\Promotion\Model\Bogo;
use Magento\Backend\Model\Session;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;

class InlineEdit extends \Magento\Backend\App\Action
{
    /**
     * @var Bogo
     */
    protected $bogoModel;

    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    public function __construct(
        Action\Context $context,
        JsonFactory $jsonFactory,
        Bogo $bogoModel
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->bogoModel = $bogoModel;
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

        foreach (array_keys($postItems) as $bogoId) {
            /** @var Bogo $bogo */
            $bogo = $this->bogoModel->load($bogoId);
            try {
                $bogoData = $postItems[$bogoId];
                $extendedBogoData = $bogo->getData();
                $this->setBogoData($bogo, $extendedBogoData, $bogoData);
                $this->bogoModel->save($bogo);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithExtraChargeId($bogo, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithExtraChargeId($bogo, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithExtraChargeId(
                    $bogo,
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
     * Add Bogo name to error message
     *
     * @param Bogo $bogo
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithExtraChargeId(Bogo $bogo, $errorText)
    {
        return '[Bogo ID: ' . $bogo->getId() . '] ' . $errorText;
    }

    /**
     * Set Bogo data
     *
     * @param Bogo $bogo
     * @param array $extendedBogoData
     * @param array $bogoData
     * @return $this
     */
    public function setBogoData(Bogo $bogo, array $extendedBogoData, array $bogoData)
    {
        $bogo->setData(array_merge($bogo->getData(), $extendedBogoData, $bogoData));
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zanders_Promotion::bogo');
    }
}
