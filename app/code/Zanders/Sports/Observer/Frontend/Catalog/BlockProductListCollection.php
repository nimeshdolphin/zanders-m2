<?php

declare(strict_types=1);

namespace Zanders\Sports\Observer\Frontend\Catalog;

class BlockProductListCollection implements \Magento\Framework\Event\ObserverInterface
{

    protected $_coreSession;

    public function __construct(
        \Magento\Framework\Session\SessionManagerInterface $coreSession
        ){
        $this->_coreSession = $coreSession;
    }


    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {       
       
       $this->_coreSession->start();
       if ($this->_coreSession->getHideOutOfStock() == 'y') 
       {
           $productcollection = $observer->getCollection();
           $productcollection->joinField('qty', 'cataloginventory_stock_item', 'qty', 'product_id=entity_id', "{{table}}.qty>0.00"); 
       }  

       $productcoll = $observer->getEvent()->getCollection();
       $productcoll->joinField('available', 'zanders_available', 'available', 'product_id=entity_id', null, 'left');    
        
        
    }
}

