<?php declare (strict_types = 1);

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

class DynamicRulesRepository implements DynamicRulesRepositoryInterface {

	protected $_productCollectionFactory;

	protected $_categoryFactory;

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
		\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
		\Magento\Catalog\Model\CategoryFactory $categoryFactory,
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
		$this->_productCollectionFactory = $productCollectionFactory;
		$this->_categoryFactory = $categoryFactory;
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
	public function update(
		$Id, $source_category, $source_attributes, $products, $weight
	) {
		/* if (empty($dynamicRules->getStoreId())) {
			                        $storeId = $this->storeManager->getStore()->getId();
			                        $dynamicRules->setStoreId($storeId);
		*/
		try
		{
			$dynamicRulesModel = $this->dynamicRulesFactory->create();
			$dynamicRulesModel->load($Id);

			if ($source_category == '') {
				throw new CouldNotSaveException(__('Source Category Required'));
			}

			if ($source_attributes == '') {
				throw new CouldNotSaveException(__('Source Attributes Required'));
			}

			if ($source_category) {

				if (!is_numeric($source_category)) {
					throw new CouldNotSaveException(__('Wrong format for source category'));
				}

				$category = $this->_categoryFactory->create()->load($source_category);
				if (!$category->getId()) {
					throw new CouldNotSaveException(__('Source category ID incorrect'));
				}

			}

			if ($products) {
				$Ids = explode(',', $products);

				$collection = $this->_productCollectionFactory->create();
				$collection->addAttributeToSelect('*');
				$collection->addFieldToFilter('entity_id', ['in' => $Ids]);
				$foundIds = $collection->getAllIds();

				$produc = array_filter(explode(',', $products));
				foreach ($produc as $product) {
					if (!in_array($product, $foundIds)) {
						throw new CouldNotSaveException(__('Product Id no exists'));
					}
				}

			}

			if($source_category != '')
			{
				$dynamicRulesModel->setSourceCategory($source_category);	
			}

			if($source_attributes != '')
			{
				$dynamicRulesModel->setSourceAttributes($source_attributes);
			}
			
			if($products != '')
			{
				$dynamicRulesModel->setProducts($products);
			}	
			
			if($weight)
			{
				$dynamicRulesModel->setWeight($weight);	
			}
			
			$dynamicRulesModel->save();

		} catch (\Exception $exception) {
			throw new CouldNotSaveException(__(
				$exception->getMessage()
			));
		}
		return $dynamicRulesModel->getDataModel();
	}

	/**
	 * {@inheritdoc}
	 */
	public function save(
		$source_category, $source_attributes, $products, $weight
	) {
		/* if (empty($dynamicRules->getStoreId())) {
			            $storeId = $this->storeManager->getStore()->getId();
			            $dynamicRules->setStoreId($storeId);
		*/
		try
		{

			if ($source_category == '') {
				throw new CouldNotSaveException(__('Source Category Required'));
			}

			if ($source_attributes == '') {
				throw new CouldNotSaveException(__('Source Attributes Required'));
			}

			if ($source_category) {

				if (!is_numeric($source_category)) {
					throw new CouldNotSaveException(__('Wrong format for source category'));
				}

				$category = $this->_categoryFactory->create()->load($source_category);
				if (!$category->getId()) {
					throw new CouldNotSaveException(__('Source category ID incorrect'));
				}

			}

			if ($products) {
				$Ids = explode(',', $products);

				$collection = $this->_productCollectionFactory->create();
				$collection->addAttributeToSelect('*');
				$collection->addFieldToFilter('entity_id', ['in' => $Ids]);
				$foundIds = $collection->getAllIds();

				$produc = array_filter(explode(',', $products));
				foreach ($produc as $product) {
					if (!in_array($product, $foundIds)) {
						throw new CouldNotSaveException(__('Product Id no exists'));
					}
				}

			}

			$dynamicRulesData = array('source_category' => $source_category, 'source_attributes' => $source_attributes, 'products' => $products, 'weight' => $weight);

			$dynamicRulesModel = $this->dynamicRulesFactory->create()->setData($dynamicRulesData);

			$this->resource->save($dynamicRulesModel);
		} catch (\Exception $exception) {
			throw new CouldNotSaveException(__(
				$exception->getMessage()
			));
		}
		return $dynamicRulesModel->getDataModel();
	}

	/**
	 * {@inheritdoc}
	 */
	public function get($dynamicRulesId) {
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
	public function deleteById($Id) {
		return $this->delete($this->get($Id));
	}
}
