<?php
/**
 * @category   Zanders
 * @package    Zanders_Promotion
 */
 
namespace Zanders\Promotion\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Zanders\Promotion\Model\Promotion;
use Magento\Backend\Model\Session;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;

class InlineEdit extends \Magento\Backend\App\Action
{
    /**
     * @var Promotion
     */
    protected $promotionModel;

    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    public function __construct(
        Action\Context $context,
        JsonFactory $jsonFactory,
        Promotion $promotionModel
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->promotionModel = $promotionModel;
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

        foreach (array_keys($postItems) as $promotionId) {
            /** @var Promotion $promotion */
            $promotion = $this->promotionModel->load($promotionId);
            try {
                $promotionData = $postItems[$promotionId];
                $extendedPromotionData = $promotion->getData();
                $this->setPromotionData($promotion, $extendedPromotionData, $promotionData);
                $this->promotionModel->save($promotion);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithExtraChargeId($promotion, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithExtraChargeId($promotion, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithExtraChargeId(
                    $promotion,
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
     * Add Promotion name to error message
     *
     * @param Promotion $promotion
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithExtraChargeId(Promotion $promotion, $errorText)
    {
        return '[Promotion ID: ' . $promotion->getId() . '] ' . $errorText;
    }

    /**
     * Set Promotion data
     *
     * @param Promotion $promotion
     * @param array $extendedPromotionData
     * @param array $promotionData
     * @return $this
     */
    public function setPromotionData(Promotion $promotion, array $extendedPromotionData, array $promotionData)
    {
        $promotion->setData(array_merge($promotion->getData(), $extendedPromotionData, $promotionData));
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zanders_Promotion::promotion');
    }
}
