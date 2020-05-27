<?php
namespace Zanders\Sports\Controller\Index;

use Magento\Framework\Controller\ResultFactory;

class Outofstock extends \Magento\Framework\App\Action\Action
{
	protected $_coreSession;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\Session\SessionManagerInterface $coreSession
	){
		$this->_coreSession = $coreSession;
		return parent::__construct($context);
	}

	public function execute()
	{
		$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
		$this->_coreSession->start();
		if ($this->_coreSession->getHideOutOfStock()) {
			if ($this->_coreSession->getHideOutOfStock() == 'y') {
				$this->_coreSession->setHideOutOfStock('n');
			} else {
				$this->_coreSession->setHideOutOfStock('y');
			}
		} else {
			$this->_coreSession->setHideOutOfStock('y');
		}
		$resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
	}
}