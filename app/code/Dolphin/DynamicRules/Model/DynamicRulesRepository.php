<?php declare(strict_types=1);


namespace Dolphin\DynamicRules\Model;

use Dolphin\DynamicRules\Api\Data\DynamicRulesInterfaceFactory;
use Dolphin\DynamicRules\Api\Data\DynamicRulesSearchResultsInterfaceFactory;
use Dolphin\DynamicRules\Api\DynamicRulesRepositoryInterface;
use Dolphin\DynamicRules\Model\ResourceModel\DynamicRules as ResourceDynamicRules;
use Dolphin\DynamicRules\Model\ResourceModel\DynamicRules\CollectionFactory as DynamicRulesCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;


class DynamicRulesRepository implements DynamicRulesRepositoryInterface
{

    protected $extensionAttributesJoinProcessor;

    protected $searchResultsFactory;

    private $storeManager;

    protected $dataObjectProcessor;

    protected $dynamicRulesFactory;

    protected $dataObjectHelper;

    protected $extensibleDataObjectConverter;
    protected $resource;

    private $collectionProcessor;

    protected $dynamicRulesCollectionFactory;

    protected $dataDynamicRulesFactory;


    /**
     * @param ResourceDynamicRules $resource
     * @param DynamicRulesFactory $dynamicRulesFactory
     * @param DynamicRulesInterfaceFactory $dataDynamicRulesFactory
     * @param DynamicRulesCollectionFactory $dynamicRulesCollectionFactory
     * @param DynamicRulesSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceDynamicRules $resource,
        DynamicRulesFactory $dynamicRulesFactory,
        DynamicRulesInterfaceFactory $dataDynamicRulesFactory,
        DynamicRulesCollectionFactory $dynamicRulesCollectionFactory,
        DynamicRulesSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->dynamicRulesFactory = $dynamicRulesFactory;
        $this->dynamicRulesCollectionFactory = $dynamicRulesCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataDynamicRulesFactory = $dataDynamicRulesFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Dolphin\DynamicRules\Api\Data\DynamicRulesInterface $dynamicRules
    ) {
        /* if (empty($dynamicRules->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $dynamicRules->setStoreId($storeId);
        } */
        
        $dynamicRulesData = $this->extensibleDataObjectConverter->toNestedArray(
            $dynamicRules,
            [],
            \Dolphin\DynamicRules\Api\Data\DynamicRulesInterface::class
        );
        
        $dynamicRulesModel = $this->dynamicRulesFactory->create()->setData($dynamicRulesData);
        
        try {
            $this->resource->save($dynamicRulesModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the dynamicRules: %1',
                $exception->getMessage()
            ));
        }
        return $dynamicRulesModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($dynamicRulesId)
    {
        $dynamicRules = $this->dynamicRulesFactory->create();
        $this->resource->load($dynamicRules, $dynamicRulesId);
        if (!$dynamicRules->getId()) {
            throw new NoSuchEntityException(__('DynamicRules with id "%1" does not exist.', $dynamicRulesId));
        }
        return $dynamicRules->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->dynamicRulesCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Dolphin\DynamicRules\Api\Data\DynamicRulesInterface::class
        );
        
        $this->collectionProcessor->process($criteria, $collection);
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        
        $items = [];
        foreach ($collection as $model) {
            $items[] = $model->getDataModel();
        }
        
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Dolphin\DynamicRules\Api\Data\DynamicRulesInterface $dynamicRules
    ) {
        try {
            $dynamicRulesModel = $this->dynamicRulesFactory->create();
            $this->resource->load($dynamicRulesModel, $dynamicRules->getId());
            $this->resource->delete($dynamicRulesModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the DynamicRules: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($dynamicRulesId)
    {
        return $this->delete($this->get($dynamicRulesId));
    }
}

