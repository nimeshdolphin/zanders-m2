<?php

declare(strict_types=1);

namespace Zanders\Sports\Observer\Frontend\Catalog;

class ControllerProductView implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var Magento\CatalogInventory\Api\StockStateInterface 
     */
    protected $_stockStateInterface;

    /**
     * @var Magento\CatalogInventory\Api\StockRegistryInterface 
     */
    protected $_stockRegistry;

    protected $_availability;
    protected $_elliottQuery;

    public function __construct(
        \Magento\CatalogInventory\Api\StockStateInterface $stockStateInterface,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry, 
        \Zanders\Sports\Model\AvailabilityFactory $avalibility,
        \Zanders_Elliott_Model_Elliott_Query $elliottQuery
    )
    {
         $this->_stockStateInterface = $stockStateInterface;
         $this->_stockRegistry = $stockRegistry;
         $this->_availability = $avalibility;
         $this->_elliottQuery = $elliottQuery;
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
        $product = $observer->getEvent()->getProduct();
        $stock = $this->_stockRegistry->getStockItem($product->getId());
        $available = $this->_availability->create()->load($product->getSku());

        $zavail = $this->_elliottQuery->getInventory($product->getSku());

        if($product->getPrice() != $zavail['price']){
            $product->addAttributeUpdate('price',$zavail['price'],$product->getStoreId());
        }


        if($stock->getQty()!=$zavail['inv'] || $stock->getQty()<0){
            if($zavail>0){
                $stock->setQty($zavail['inv']);
                if(!$stock->getIsInStock()){
                    $stock->setIsInStock(true);
                }
            }else{
                $stock->setQty(0);
            }
            $stock->save();

        }


        if($available->getAvailable()!=$zavail['inv'] && $available->getSku()){
            if($zavail['inv']>0){
                $available->setAvailable($zavail['inv'])->save();
            }else{
                $available->setAvailable(0)->save();
            }
        }
        
    }
}

