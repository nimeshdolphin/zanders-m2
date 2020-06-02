<?php
/**
 * @category   Zanders
 * @package    Zanders_Manufacturer
 */

namespace Zanders\Manufacturer\Model;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Zanders\Manufacturer\Api\Data;
use Zanders\Manufacturer\Api\ManufacturerRepositoryInterface;
use Zanders\Manufacturer\Model\ResourceModel\Manufacturer as ResourceManufacturer;
use Zanders\Manufacturer\Model\ResourceModel\Manufacturer\CollectionFactory as ManufacturerCollectionFactory;

class ManufacturerRepository implements ManufacturerRepositoryInterface {
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
	private function getUserContext(): UserContextInterface {
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
	private function getAuthorization(): AuthorizationInterface {
		if (!$this->authorization) {
			$this->authorization = ObjectManager::getInstance()->get(AuthorizationInterface::class);
		}

		return $this->authorization;
	}

	/**
	 * Save Manufacturer data
	 * @param  string $name
	 * @param  string $address
	 * @param  string $serialized_display_on
	 * @param  string $serialized_text
	 * @param  string $phone
	 * @param  string $web
	 * @param  string $enable
	 * @param  string $image
	 * @param  string $image_type
	 * @return Manufacturer
	 * @throws CouldNotSaveException
	 */
	public function saveManufacture($name, $address, $serialized_display_on, $serialized_text, $phone, $web, $enable, $image, $image_type) {
		try
		{
			if ($name == '') {
				throw new CouldNotSaveException(__('Name Required'));
			}

			$_mimeTypes = array(
				'image/jpg' => 'jpg',
				'image/jpeg' => 'jpg',
				'image/gif' => 'gif',
				'image/png' => 'png',
			);

			$dynamicRulesData = array('name' => $name, 'address' => $address, 'serialized_display_on' => $serialized_display_on, 'serialized_text' => $serialized_text, 'phone' => $phone, 'web' => $web, 'enable' => $enable);
			$manufacturerModel = $this->manufacturerFactory->create()->setData($dynamicRulesData);
			$this->resource->save($manufacturerModel);

			if (($image != '' && isset($image)) && ($image_type != '' && isset($image_type))) {

				$imagetype = $_mimeTypes[$image_type];
				if ($imagetype) {
					$file = $this->storeManager->getStore()->getBaseMediaDir() . "/manufacturers/" . $manufacturerModel->getId() . '.' . $imagetype;
					file_put_contents($file, base64_decode($image));
					$manufacturerModel->setImageType($imagetype);
					$media_path = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . "manufacturers/" . $manufacturerModel->getId() . '.' . $imagetype;
					$manufacturerModel->setImage($imagetype);
					$manufacturerModel->save();
				}
			}


			$media_path = null;		
			if($manufacturerModel->getImageType())
			{
				 $media_path = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . "manufacturers/" . $manufacturerModel->getId() . '.' . $manufacturerModel->getImageType();							 
			}
			 
			$data[] = array('id'=>$manufacturerModel->getId(),'name'=>$manufacturerModel->getName(), 'address' => $manufacturerModel->getAddress(), 'serialized_display_on'=>$manufacturerModel->getSerializedDisplayOn(), 'serialized_text'=>$manufacturerModel->getSerializedText(), 'phone'=>$manufacturerModel->getPhone(),'web'=>$manufacturerModel->getWeb(), 'enable'=>$manufacturerModel->getEnable(), 'image'=>$media_path, 'image_type'=>$manufacturerModel->getImageType());

			return $data;


		} catch (\Exception $exception) {
			throw new CouldNotSaveException(
				__($exception->getMessage()),
				$exception
			);
		}

	}

	/**
	 * Load Manufacturer data by given Manufacturer Identity
	 *
	 * @param string $manufacturerId
	 * @return Manufacturer
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
	 */
	public function getById($manufacturerId) {
		$manufacturer = $this->manufacturerFactory->create();
		$manufacturer->load($manufacturerId);
		if($manufacturer->getImageType())
		{
			$media_path = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . "manufacturers/" . $manufacturer->getId() . '.' . $manufacturer->getImageType();
			if(@getimagesize($media_path))
			{
				$manufacturer->setImage($media_path);	
			}
			
		}
		if (!$manufacturer->getId()) {
			throw new NoSuchEntityException(__('The manufacturer with the "%1" ID doesn\'t exist.', $manufacturerId));
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
	public function getManufactureById($manufacturerId) {
		$manufacturer = $this->manufacturerFactory->create();
		$manufacturer->load($manufacturerId);
		$media_path = null;					
		if($manufacturer->getImageType())
		{
			$media_path = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . "manufacturers/" . $manufacturer->getId() . '.' . $manufacturer->getImageType();			
		}
		if (!$manufacturer->getId()) {
			throw new NoSuchEntityException(__('The manufacturer with the "%1" ID doesn\'t exist.', $manufacturerId));
		}

		$data = array(array('id'=>$manufacturer->getId(),'name'=>$manufacturer->getName(), 'address' => $manufacturer->getAddress(), 'serialized_display_on'=>$manufacturer->getSerializedDisplayOn(), 'serialized_text'=>$manufacturer->getSerializedText(), 'phone'=>$manufacturer->getPhone(),'web'=>$manufacturer->getWeb(), 'enable'=>$manufacturer->getEnable(), 'image'=>$media_path, 'image_type'=>$manufacturer->getImageType()));

		return $data;
	}

	/**
	 * Load Manufacturer data collection by given search criteria
	 *
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 * @return \Zanders\Manufacturer\Api\Data\ManufacturerSearchResultsInterface
	 */
	public function getList() {
		/** @var \Zanders\Manufacturer\Model\ResourceModel\Manufacturer\Collection $collection */
		$collection = $this->manufacturerCollectionFactory->create();

		//$this->collectionProcessor->process($criteria, $collection);

		/** @var Data\ManufacturerSearchResultsInterface $searchResults */
		$searchResults = $this->searchResultsFactory->create();
		//$searchResults->setSearchCriteria($criteria);
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
	public function delete(\Zanders\Manufacturer\Api\Data\ManufacturerInterface $manufacturer) {
		try {
			$this->resource->delete($manufacturer);
		} catch (\Exception $exception) {
			throw new CouldNotDeleteException(
				__('Could not delete the manufacturer: %1', $exception->getMessage())
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
	public function deleteById($manufacturerId) {
		return $this->delete($this->getById($manufacturerId));
	}

	/**
	 * Retrieve collection processor
	 *
	 * @deprecated 102.0.0
	 * @return CollectionProcessorInterface
	 */
	private function getCollectionProcessor() {
		if (!$this->collectionProcessor) {
			$this->collectionProcessor = \Magento\Framework\App\ObjectManager::getInstance()->get(
				'Zanders\Manufacturer\Model\Api\SearchCriteria\ManufacturerCollectionProcessor'
			);
		}
		return $this->collectionProcessor;
	}

	/**
	 * Save Manufacturer data
	 * @param  string $id
	 * @param  string $name
	 * @param  string $address
	 * @param  string $serialized_display_on
	 * @param  string $serialized_text
	 * @param  string $phone
	 * @param  string $web
	 * @param  string $enable
	 * @param  string $image
	 * @param  string $image_type
	 * @return Manufacturer
	 * @throws CouldNotSaveException
	 */
	public function updateManufacture($id, $name, $address, $serialized_display_on, $serialized_text, $phone, $web, $enable, $image, $image_type) {
		try
		{

			$manufacturerModel = $this->manufacturerFactory->create();
			$manufacturerModel->load($id);

			$_mimeTypes = array(
				'image/jpg' => 'jpg',
				'image/jpeg' => 'jpg',
				'image/gif' => 'gif',
				'image/png' => 'png',
			);

			if (isset($name) && $name != '') {
				$manufacturerModel->setName($name);
			}

			if (isset($address) && $address != '') {
				$manufacturerModel->setAddress($address);
			}

			if (isset($serialized_display_on) && $serialized_display_on != '') {
				$manufacturerModel->setSerializedDisplayOn($serialized_display_on);
			}

			if (isset($serialized_text) && $serialized_text != '') {
				$manufacturerModel->setSerializedText($serialized_text);
			}

			if (isset($phone) && $phone != '') {
				$manufacturerModel->setPhone($phone);
			}

			if (isset($web) && $web != '') {
				$manufacturerModel->setWeb($web);
			}

			if ($enable=='1' or $enable=='0') {
				$manufacturerModel->setEnable($enable);
			}

			//$this->resource->save($manufacturerModel);

			if (($image != '' && isset($image)) && ($image_type != '' && isset($image_type))) {

				$imagetype = $_mimeTypes[$image_type];
				if ($imagetype) {
					$file = $this->storeManager->getStore()->getBaseMediaDir() . "/manufacturers/" . $manufacturerModel->getId() . '.' . $imagetype;
					file_put_contents($file, base64_decode($image));
					$manufacturerModel->setImageType($imagetype);
					$media_path = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . "manufacturers/" . $manufacturerModel->getId() . '.' . $imagetype;
					$manufacturerModel->setImage($media_path);

				}
			}

			$manufacturerModel->save();

			$media_path = null;		
			if($manufacturerModel->getImageType())
			{
				 $media_path = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . "manufacturers/" . $manufacturerModel->getId() . '.' . $manufacturerModel->getImageType();							 
			}
			 
			$data[] = array('id'=>$manufacturerModel->getId(),'name'=>$manufacturerModel->getName(), 'address' => $manufacturerModel->getAddress(), 'serialized_display_on'=>$manufacturerModel->getSerializedDisplayOn(), 'serialized_text'=>$manufacturerModel->getSerializedText(), 'phone'=>$manufacturerModel->getPhone(),'web'=>$manufacturerModel->getWeb(), 'enable'=>$manufacturerModel->getEnable(), 'image'=>$media_path, 'image_type'=>$manufacturerModel->getImageType());

			return $data;

		} catch (\Exception $exception) {
			throw new CouldNotSaveException(
				__($exception->getMessage()),
				$exception
			);
		}

	}


	/**
	 * Load Manufacturer data collection by given search criteria
	 *
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 * @return string
	 */
	public function getManufacturerList() {
		/** @var \Zanders\Manufacturer\Model\ResourceModel\Manufacturer\Collection $collection */
		$collection = $this->manufacturerCollectionFactory->create();

		foreach ($collection as $manufacturer) {	
			$media_path = null;		
			if($manufacturer->getImageType())
			{
				 $media_path = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . "manufacturers/" . $manufacturer->getId() . '.' . $manufacturer->getImageType();							 
			}
			 
			$data[] = array('id'=>$manufacturer->getId(),'name'=>$manufacturer->getName(), 'address' => $manufacturer->getAddress(), 'serialized_display_on'=>$manufacturer->getSerializedDisplayOn(), 'serialized_text'=>$manufacturer->getSerializedText(), 'phone'=>$manufacturer->getPhone(),'web'=>$manufacturer->getWeb(), 'enable'=>$manufacturer->getEnable(), 'image'=>$media_path, 'image_type'=>$manufacturer->getImageType());	
		}
		return $data;
	}

	/**
	 * Save Manufacturer data
	 * @param  string $name
	 * @param  string $address
	 * @param  string $serialized_display_on
	 * @param  string $serialized_text
	 * @param  string $phone
	 * @param  string $web
	 * @param  string $enable
	 * @param  string $image
	 * @param  string $imagetype
	 * @return Manufacturer
	 * @throws CouldNotSaveException
	 */
	public function save($name, $address, $serialized_display_on, $serialized_text, $phone, $web, $enable, $image, $imagetype) {
		try
		{
			if ($name == '') {
				throw new CouldNotSaveException(__('Name Required'));
			}

			$_mimeTypes = array(
				'image/jpg' => 'jpg',
				'image/jpeg' => 'jpg',
				'image/gif' => 'gif',
				'image/png' => 'png',
			);

			$dynamicRulesData = array('name' => $name, 'address' => $address, 'serialized_display_on' => $serialized_display_on, 'serialized_text' => $serialized_text, 'phone' => $phone, 'web' => $web, 'enable' => $enable);
			$manufacturerModel = $this->manufacturerFactory->create()->setData($dynamicRulesData);
			$this->resource->save($manufacturerModel);

			if ($image != '' && $imagetype != '') {

				$imagetype = $_mimeTypes[$imagetype];
				if ($imagetype) {
					$file = $this->storeManager->getStore()->getBaseMediaDir() . "/manufacturers/" . $manufacturerModel->getId() . '.' . $imagetype;
					file_put_contents($file, base64_decode($image));
					$manufacturerModel->setImageType($imagetype);
					$media_path = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . "manufacturers/" . $manufacturerModel->getId() . '.' . $imagetype;
					$manufacturerModel->setImage($imagetype);
					$manufacturerModel->save();
				}
			}

			return $manufacturerModel;

		} catch (\Exception $exception) {
			throw new CouldNotSaveException(
				__($exception->getMessage()),
				$exception
			);
		}

	}
}
