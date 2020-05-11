<?php
namespace Zanders\Theme\Block;
class Outofstock extends \Magento\Framework\View\Element\Template
{
    protected $_coreSession;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Session\SessionManagerInterface $coreSession
    ){
        $this->_coreSession = $coreSession;
        parent::__construct($context);
    }

    public function getOutOfStockSession()
    {
        $this->_coreSession->start();
        return $this->_coreSession->getHideOutOfStock();
    }
}