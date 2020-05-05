<?php
/**
 * @category   Zanders
 * @package    Zanders_Manufacturer
 */

namespace Zanders\Manufacturer\Model;

use Zanders\Manufacturer\Api\Data;
use Zanders\Manufacturer\Api\ManufacturerRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Zanders\Manufacturer\Model\ResourceModel\Manufacturer as ResourceManufacturer;
use Zanders\Manufacturer\Model\ResourceModel\Manufacturer\CollectionFactory as ManufacturerCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\AuthorizationInterface;
use Magento\Authorization\Model\UserContextInterface;

class ManufacturerRepository implements ManufacturerRepositoryInterface
{
    /**
     * @var ResourceManufacturer
     */
    protected $resource;

    /**
     * @var ManufacturerFactory
     */
    protected $manufacturerFactory;

    /**
     * @var ManufacturerCollectionFactory
     */
    protected $manufacturerCollectionFactory;

    /**
     * @var Data\ManufacturerSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var \Zanders\Manufacturer\Api\Data\ManufacturerInterfaceFactory
     */
    protected $dataManufacturerFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * @param ResourceManufacturer $resource
     * @param ManufacturerFactory $manufacturerFactory
     * @param Data\ManufacturerInterfaceFactory $dataManufacturerFactory
     * @param ManufacturerCollectionFactory $manufacturerCollectionFactory
     * @param Data\ManufacturerSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ResourceManufacturer $resource,
        ManufacturerFactory $manufacturerFactory,
        Data\ManufacturerInterfaceFactory $dataManufacturerFactory,
        ManufacturerCollectionFactory $manufacturerCollectionFactory,
        Data\ManufacturerSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor = null
    ) {
        $this->resource = $resource;
        $this->manufacturerFactory = $manufacturerFactory;
        $this->manufacturerCollectionFactory = $manufacturerCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataManufacturerFactory = $dataManufacturerFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor ?: $this->getCollectionProcessor();
    }

    /**
     * Get user context.
     *
     * @return UserContextInterface
     */
    private function getUserContext(): UserContextInterface
    {
        if (!$this->userContext) {
            $this->userContext = ObjectManager::getInstance()->get(UserContextInterface::class);
        }

        return $this->userContext;
    }

    /**
     * Get authorization service.
     *
     * @return AuthorizationInterface
     */
    private function getAuthorization(): AuthorizationInterface
    {
        if (!$this->authorization) {
            $this->authorization = ObjectManager::getInstance()->get(AuthorizationInterface::class);
        }

        return $this->authorization;
    }

    /**
     * Save Manufacturer data
     *
     * @param \Zanders\Manufacturer\Api\Data\ManufacturerInterface|Manufacturer $manufacturer
     * @return Manufacturer
     * @throws CouldNotSaveException
     */
    public function save(\Zanders\Manufacturer\Api\Data\ManufacturerInterface $manufacturer)
    {
        try {
            $this->resource->save($manufacturer);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save the Payment Extra Charge: %1', $exception->getMessage()),
                $exception
            );
        }
        return $manufacturer;
    }

    /**
     * Load Manufacturer data by given Manufacturer Identity
     *
     * @param string $manufacturerId
     * @return Manufacturer
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($manufacturerId)
    {
        $manufacturer = $this->manufacturerFactory->create();
        $manufacturer->load($manufacturerId);
        if (!$manufacturer->getId()) {
            throw new NoSuchEntityException(__('The Payment Extra Charge with the "%1" ID doesn\'t exist.', $manufacturerId));
        }
        return $manufacturer;
    }

    /**
     * Load Manufacturer data collection by given search criteria
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \Zanders\Manufacturer\Api\Data\ManufacturerSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        /** @var \Zanders\Manufacturer\Model\ResourceModel\Manufacturer\Collection $collection */
        $collection = $this->manufacturerCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        /** @var Data\ManufacturerSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * Delete Manufacturer
     *
     * @param \Zanders\Manufacturer\Api\Data\ManufacturerInterface $manufacturer
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(\Zanders\Manufacturer\Api\Data\ManufacturerInterface $manufacturer)
    {
        try {
            $this->resource->delete($manufacturer);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __('Could not delete the Payment Extra Charge: %1', $exception->getMessage())
            );
        }
        return true;
    }

    /**
     * Delete Manufacturer by given Manufacturer Identity
     *
     * @param string $manufacturerId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($manufacturerId)
    {
        return $this->delete($this->getById($manufacturerId));
    }

    /**
     * Retrieve collection processor
     *
     * @deprecated 102.0.0
     * @return CollectionProcessorInterface
     */
    private function getCollectionProcessor()
    {
        if (!$this->collectionProcessor) {
            $this->collectionProcessor = \Magento\Framework\App\ObjectManager::getInstance()->get(
                'Zanders\Manufacturer\Model\Api\SearchCriteria\ManufacturerCollectionProcessor'
            );
        }
        return $this->collectionProcessor;
    }
}
