<?php
namespace Zanders\Sports\Block;

class Manufacturers extends \Magento\Catalog\Block\Product\AbstractProduct
{
    protected $manufacturersmodelFactory;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Zanders\Manufacturer\Model\ResourceModel\Manufacturer\CollectionFactory $manufacturersmodelFactory,
        array $data = []
    ) {
        $this->manufacturersmodelFactory = $manufacturersmodelFactory;
        parent::__construct($context, $data);
    }

    public function getCollection() {
        return $this->manufacturersmodelFactory->create()->addFilter('enable',1)->setOrder('name', 'ASC');
    }
}