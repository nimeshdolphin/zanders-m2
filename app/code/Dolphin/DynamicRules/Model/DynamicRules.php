<?php declare(strict_types=1);


namespace Dolphin\DynamicRules\Model;

use Dolphin\DynamicRules\Api\Data\DynamicRulesInterface;
use Dolphin\DynamicRules\Api\Data\DynamicRulesInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;


class DynamicRules extends \Magento\Framework\Model\AbstractModel
{

    protected $dataObjectHelper;

    protected $_eventPrefix = 'dolphin_dynamicrules_dynamicrules';
    protected $dynamicrulesDataFactory;


    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param DynamicRulesInterfaceFactory $dynamicrulesDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Dolphin\DynamicRules\Model\ResourceModel\DynamicRules $resource
     * @param \Dolphin\DynamicRules\Model\ResourceModel\DynamicRules\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        DynamicRulesInterfaceFactory $dynamicrulesDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Dolphin\DynamicRules\Model\ResourceModel\DynamicRules $resource,
        \Dolphin\DynamicRules\Model\ResourceModel\DynamicRules\Collection $resourceCollection,
        array $data = []
    ) {
        $this->dynamicrulesDataFactory = $dynamicrulesDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve dynamicrules model with dynamicrules data
     * @return DynamicRulesInterface
     */
    public function getDataModel()
    {
        $dynamicrulesData = $this->getData();
        
        $dynamicrulesDataObject = $this->dynamicrulesDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $dynamicrulesDataObject,
            $dynamicrulesData,
            DynamicRulesInterface::class
        );
        
        return $dynamicrulesDataObject;
    }
}

