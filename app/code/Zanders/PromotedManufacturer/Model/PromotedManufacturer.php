<?php declare(strict_types=1);


namespace Zanders\PromotedManufacturer\Model;

use Magento\Framework\Api\DataObjectHelper;
use Zanders\PromotedManufacturer\Api\Data\PromotedManufacturerInterface;
use Zanders\PromotedManufacturer\Api\Data\PromotedManufacturerInterfaceFactory;


class PromotedManufacturer extends \Magento\Framework\Model\AbstractModel
{

    protected $dataObjectHelper;

    protected $_eventPrefix = 'zanders_promotedmanufacturer_promotedmanufacturer';
    protected $promotedmanufacturerDataFactory;


    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param PromotedManufacturerInterfaceFactory $promotedmanufacturerDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Zanders\PromotedManufacturer\Model\ResourceModel\PromotedManufacturer $resource
     * @param \Zanders\PromotedManufacturer\Model\ResourceModel\PromotedManufacturer\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        PromotedManufacturerInterfaceFactory $promotedmanufacturerDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Zanders\PromotedManufacturer\Model\ResourceModel\PromotedManufacturer $resource,
        \Zanders\PromotedManufacturer\Model\ResourceModel\PromotedManufacturer\Collection $resourceCollection,
        array $data = []
    ) {
        $this->promotedmanufacturerDataFactory = $promotedmanufacturerDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve promotedmanufacturer model with promotedmanufacturer data
     * @return PromotedManufacturerInterface
     */
    public function getDataModel()
    {
        $promotedmanufacturerData = $this->getData();
        
        $promotedmanufacturerDataObject = $this->promotedmanufacturerDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $promotedmanufacturerDataObject,
            $promotedmanufacturerData,
            PromotedManufacturerInterface::class
        );
        
        return $promotedmanufacturerDataObject;
    }
}

