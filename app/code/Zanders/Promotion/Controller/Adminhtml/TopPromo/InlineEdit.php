<?php
/**
 * @category   Zanders
 * @package    Zanders_Promotion
 */
 
namespace Zanders\Promotion\Controller\Adminhtml\TopPromo;

use Magento\Backend\App\Action;
use Zanders\Promotion\Model\TopPromo;
use Magento\Backend\Model\Session;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;

class InlineEdit extends \Magento\Backend\App\Action
{
    /**
     * @var TopPromo
     */
    protected $topPromoModel;

    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    public function __construct(
        Action\Context $context,
        JsonFactory $jsonFactory,
        TopPromo $topPromoModel
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->topPromoModel = $topPromoModel;
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

        foreach (array_keys($postItems) as $topPromoId) {
            /** @var TopPromo $topPromo */
            $topPromo = $this->topPromoModel->load($topPromoId);
            try {
                $topPromoData = $postItems[$topPromoId];
                $extendedTopPromoData = $topPromo->getData();
                $this->setTopPromoData($topPromo, $extendedTopPromoData, $topPromoData);
                $this->topPromoModel->save($topPromo);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithExtraChargeId($topPromo, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithExtraChargeId($topPromo, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithExtraChargeId(
                    $topPromo,
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
     * Add TopPromo name to error message
     *
     * @param TopPromo $topPromo
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithExtraChargeId(TopPromo $topPromo, $errorText)
    {
        return '[Top Promo ID: ' . $topPromo->getId() . '] ' . $errorText;
    }

    /**
     * Set TopPromo data
     *
     * @param TopPromo $topPromo
     * @param array $extendedTopPromoData
     * @param array $topPromoData
     * @return $this
     */
    public function setTopPromoData(TopPromo $topPromo, array $extendedTopPromoData, array $topPromoData)
    {
        $topPromo->setData(array_merge($topPromo->getData(), $extendedTopPromoData, $topPromoData));
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zanders_Promotion::top_promo');
    }
}
