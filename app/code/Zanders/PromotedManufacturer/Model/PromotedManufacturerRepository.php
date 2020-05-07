<?php declare (strict_types = 1);

namespace Zanders\PromotedManufacturer\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Zanders\PromotedManufacturer\Api\Data\PromotedManufacturerInterfaceFactory;
use Zanders\PromotedManufacturer\Api\Data\PromotedManufacturerSearchResultsInterfaceFactory;
use Zanders\PromotedManufacturer\Api\PromotedManufacturerRepositoryInterface;
use Zanders\PromotedManufacturer\Model\ResourceModel\PromotedManufacturer as ResourcePromotedManufacturer;
use Zanders\PromotedManufacturer\Model\ResourceModel\PromotedManufacturer\CollectionFactory as PromotedManufacturerCollectionFactory;

class PromotedManufacturerRepository implements PromotedManufacturerRepositoryInterface {

	protected $extensionAttributesJoinProcessor;

	protected $searchResultsFactory;

	private $storeManager;

	protected $dataObjectProcessor;

	protected $promotedManufacturerCollectionFactory;

	protected $dataObjectHelper;

	protected $dataPromotedManufacturerFactory;

	protected $extensibleDataObjectConverter;
	protected $resource;

	private $collectionProcessor;

	protected $promotedManufacturerFactory;

	/**
	 * @param ResourcePromotedManufacturer $resource
	 * @param PromotedManufacturerFactory $promotedManufacturerFactory
	 * @param PromotedManufacturerInterfaceFactory $dataPromotedManufacturerFactory
	 * @param PromotedManufacturerCollectionFactory $promotedManufacturerCollectionFactory
	 * @param PromotedManufacturerSearchResultsInterfaceFactory $searchResultsFactory
	 * @param DataObjectHelper $dataObjectHelper
	 * @param DataObjectProcessor $dataObjectProcessor
	 * @param StoreManagerInterface $storeManager
	 * @param CollectionProcessorInterface $collectionProcessor
	 * @param JoinProcessorInterface $extensionAttributesJoinProcessor
	 * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
	 */
	public function __construct(
		ResourcePromotedManufacturer $resource,
		PromotedManufacturerFactory $promotedManufacturerFactory,
		PromotedManufacturerInterfaceFactory $dataPromotedManufacturerFactory,
		PromotedManufacturerCollectionFactory $promotedManufacturerCollectionFactory,
		PromotedManufacturerSearchResultsInterfaceFactory $searchResultsFactory,
		DataObjectHelper $dataObjectHelper,
		DataObjectProcessor $dataObjectProcessor,
		StoreManagerInterface $storeManager,
		CollectionProcessorInterface $collectionProcessor,
		JoinProcessorInterface $extensionAttributesJoinProcessor,
		ExtensibleDataObjectConverter $extensibleDataObjectConverter
	) {
		$this->resource = $resource;
		$this->promotedManufacturerFactory = $promotedManufacturerFactory;
		$this->promotedManufacturerCollectionFactory = $promotedManufacturerCollectionFactory;
		$this->searchResultsFactory = $searchResultsFactory;
		$this->dataObjectHelper = $dataObjectHelper;
		$this->dataPromotedManufacturerFactory = $dataPromotedManufacturerFactory;
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
		$category_id, $manufacturer_id
	) {
		/* if (empty($promotedManufacturer->getStoreId())) {
			            $storeId = $this->storeManager->getStore()->getId();
			            $promotedManufacturer->setStoreId($storeId);
		*/

		$promotedManufacturerData = array('category_id' => $category_id, 'manufacturer_id' => $manufacturer_id);
		$promotedManufacturerModel = $this->promotedManufacturerFactory->create()->setData($promotedManufacturerData);

		try {
			$this->resource->save($promotedManufacturerModel);
		} catch (\Exception $exception) {
			throw new CouldNotSaveException(__(
				'Could not save the promotedManufacturer: %1',
				$exception->getMessage()
			));
		}
		return $promotedManufacturerModel->getDataModel();
	}

	/**
	 * {@inheritdoc}
	 */
	public function get($promotedManufacturerId) {
		$promotedManufacturer = $this->promotedManufacturerFactory->create();
		$this->resource->load($promotedManufacturer, $promotedManufacturerId);
		if (!$promotedManufacturer->getId()) {
			throw new NoSuchEntityException(__('PromotedManufacturer with id "%1" does not exist.', $promotedManufacturerId));
		}
		return $promotedManufacturer->getDataModel();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getList(
		\Magento\Framework\Api\SearchCriteriaInterface $criteria
	) {
		$collection = $this->promotedManufacturerCollectionFactory->create();

		$this->extensionAttributesJoinProcessor->process(
			$collection,
			\Zanders\PromotedManufacturer\Api\Data\PromotedManufacturerInterface::class
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
		\Zanders\PromotedManufacturer\Api\Data\PromotedManufacturerInterface $promotedManufacturer
	) {
		try {
			$promotedManufacturerModel = $this->promotedManufacturerFactory->create();
			$this->resource->load($promotedManufacturerModel, $promotedManufacturer->getPromotedmanufacturerId());
			$this->resource->delete($promotedManufacturerModel);
		} catch (\Exception $exception) {
			throw new CouldNotDeleteException(__(
				'Could not delete the PromotedManufacturer: %1',
				$exception->getMessage()
			));
		}
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function deleteById($promotedManufacturerId) {
		return $this->delete($this->get($promotedManufacturerId));
	}
}
