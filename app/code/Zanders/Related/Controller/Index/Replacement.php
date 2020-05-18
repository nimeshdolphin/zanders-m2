<?php declare (strict_types = 1);

namespace Zanders\Related\Controller\Index;

use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Catalog\Controller\Product as ProductAction;

class Replacement extends ProductAction implements HttpGetActionInterface, HttpPostActionInterface {

	protected $viewHelper;

	protected $resultPageFactory;

	/**
	 * Constructor
	 *
	 * @param \Magento\Framework\App\Action\Context  $context
	 * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
	 */
	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Catalog\Helper\Product\View $viewHelper
	) {
		$this->resultPageFactory = $resultPageFactory;
		$this->viewHelper = $viewHelper;
		parent::__construct($context);
	}

	/**
	 * Execute view action
	 *
	 * @return \Magento\Framework\Controller\ResultInterface
	 */
	public function execute() {

		$params = new \Magento\Framework\DataObject();

		$productId = 53508;
		$page = $this->resultPageFactory->create();
		$page->getLayout()->getUpdate()->removeHandle('default');
		$this->viewHelper->prepareAndRender($page, $productId, $this);
		//echo $page;
		//echo "aa";
		return $page;
	}
}
