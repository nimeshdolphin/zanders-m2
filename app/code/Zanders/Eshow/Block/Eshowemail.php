<?php

namespace Zanders\Eshow\Block;

use Zanders\Eshow\Model\ResourceModel\EshowPurchasedItem\CollectionFactory as EshowPurchasedItemCollectionFactory;
use Zanders\Eshow\Model\ResourceModel\EshowReceivedItem\CollectionFactory as EshowReceivedItemCollectionFactory;
use Zanders\Sports\Helper\Data as SportsHelper;

class Eshowemail extends \Magento\Framework\View\Element\Template
{
    /**
     * @var SportsHelper
     */
    protected $sportsHelper;

    protected $_productRepository;
    protected $eshowReceivedItemCollectionFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        SportsHelper $sportsHelper,
        EshowPurchasedItemCollectionFactory $eshowPurchasedItemCollectionFactory,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        EshowReceivedItemCollectionFactory $eshowReceivedItemCollectionFactory
    ) {
        
        $this->sportsHelper = $sportsHelper;
        $this->_productRepository = $productRepository;
        $this->eshowPurchasedItemCollectionFactory = $eshowPurchasedItemCollectionFactory;
        $this->eshowReceivedItemCollectionFactory = $eshowReceivedItemCollectionFactory;
        parent::__construct($context);
    }


    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->setTemplate('Zanders_Eshow::eshowemail.phtml');
        return $this;
    }

    public function getEshowSpecialPurchase($eshowId, $sku)
    {
        $savedinfo = $this->eshowPurchasedItemCollectionFactory->create();
        $savedinfo->addFieldToFilter('show_id', $eshowId)->addFieldToFilter('sku', $sku);
        return $savedinfo;
    }

    public function getEshowSpecialRecive($eshowId, $sku)
    {
        $savedinfo = $this->eshowReceivedItemCollectionFactory->create();
        $savedinfo->addFieldToFilter('show_id', $eshowId)->addFieldToFilter('sku', $sku);
        return $savedinfo;
    }

    public function getProductById($id)
    {
        return $this->_productRepository->getById($id);
    }
}
