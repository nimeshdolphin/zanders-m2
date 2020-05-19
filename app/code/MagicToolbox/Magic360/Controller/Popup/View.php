<?php
namespace MagicToolbox\Magic360\Controller\Popup;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\ObjectManager;

class View extends \Magento\Catalog\Controller\Product
{
	/**
     * @var \Magento\Catalog\Helper\Product\View
     */
    protected $viewHelper;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Catalog\Helper\Product\View $viewHelper,
		PageFactory $resultPageFactory,
		\Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
		\Psr\Log\LoggerInterface $logger = null
	){
		$this->viewHelper = $viewHelper;
		$this->resultPageFactory = $resultPageFactory;
		$this->resultForwardFactory = $resultForwardFactory;
		$this->logger = $logger ?: ObjectManager::getInstance()
            ->get(\Psr\Log\LoggerInterface::class);
		return parent::__construct($context);
	}

	public function execute()
	{
		$categoryId = (int) $this->getRequest()->getParam('category', false);
        $productId = (int) $this->getRequest()->getParam('id');
        $specifyOptions = $this->getRequest()->getParam('options');

        // Prepare helper and params
        $params = new \Magento\Framework\DataObject();
        $params->setCategoryId($categoryId);
        $params->setSpecifyOptions($specifyOptions);
        // Render page
        try {
            $page = $this->resultPageFactory->create();
            $this->viewHelper->prepareAndRender($page, $productId, $this, $params);
            return $page;
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $resultForward = $this->resultForwardFactory->create();
            $resultForward->forward('noroute');
            return $resultForward;
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $resultForward = $this->resultForwardFactory->create();
            $resultForward->forward('noroute');
            return $resultForward;
        }
    }
}