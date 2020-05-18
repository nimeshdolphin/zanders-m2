<?php declare (strict_types = 1);

namespace Dolphin\DynamicRules\Rewrite\Magento\Catalog\Block\Product\ProductList;

class Related extends \Magento\Catalog\Block\Product\ProductList\Related {

	/**
	 * @var helper
	 */
	protected $_helper;

	protected $_productCollectionFactory;

	public function __construct(
		\Dolphin\DynamicRules\Helper\Data $helper,
		\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
		\Magento\Catalog\Block\Product\Context $context,
		\Magento\Checkout\Model\ResourceModel\Cart $checkoutCart,
		\Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
		\Magento\Checkout\Model\Session $checkoutSession,
		\Magento\Framework\Module\Manager $moduleManager,
		array $data = []
	) {
		$this->_helper = $helper;
		$this->_productCollectionFactory = $productCollectionFactory;
		parent::__construct(
			$context,
			$checkoutCart,
			$catalogProductVisibility,
			$checkoutSession,
			$moduleManager,
			$data
		);

	}

	/**
	 * Prepare data
	 *
	 * @return $this
	 */
	protected function _prepareData() {
		$product = $this->getProduct();
		/* @var $product \Magento\Catalog\Model\Product */

		$this->_itemCollection = $product->getRelatedProductCollection()->addAttributeToSelect(
			'required_options'
		)->setPositionOrder()->addStoreFilter();

		$defaultProducts = array();
		$this->_itemCollection->getAllIds();
		foreach ($this->_itemCollection as $product) {
			$defaultProducts[] = $product->getId();
		}

		$dynamicproducts = $this->_helper->dynamicProducts();

		$DCollection = $this->_productCollectionFactory->create();
		//$collection->addAttributeToSelect('*');
		$DCollection->addFieldToFilter('entity_id', ['in' => $dynamicproducts]);
		$DCollection->getSelect()->order("find_in_set(entity_id,'" . implode(',', $dynamicproducts) . "')");

		$DefaultCollection = $this->_productCollectionFactory->create();
		//$collection->addAttributeToSelect('*');
		$DefaultCollection->addFieldToFilter('entity_id', ['in' => $defaultProducts]);
		$DefaultCollection->getSelect()->order("find_in_set(entity_id,'" . implode(',', $defaultProducts) . "')");

		$default = array();
		foreach ($defaultProducts as $pro) {
			if (in_array($pro, $DefaultCollection->getAllIds())) {
				$default[] = $pro;
			}
		}

		$ids = array();
		foreach ($this->_helper->dynamicProducts() as $pro) {
			if (in_array($pro, $DCollection->getAllIds())) {
				$ids[] = $pro;
			}
		}

		if (count($dynamicproducts) > 0) {
			$merged_ids = array_merge($default, $ids); // here we merge both collection ids.
		} else {
			$merged_ids = $default;
		}

		$this->_itemCollection = $this->_productCollectionFactory->create()
			->addAttributeToSelect('*')
			->addAttributeToFilter('entity_id', ['in' => $merged_ids]);

		$this->_itemCollection->getSelect()->order("find_in_set(e.entity_id,'" . implode(',', $merged_ids) . "')");

		if ($this->moduleManager->isEnabled('Magento_Checkout')) {
			$this->_addProductAttributesAndPrices($this->_itemCollection);
		}

		$this->_itemCollection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());

		$this->_itemCollection->load();

		foreach ($this->_itemCollection as $product) {
			$product->setDoNotUseCategoryId(true);
		}

		return $this;
	}

	/**
	 * Before to html handler
	 *
	 * @return $this
	 */
	protected function _beforeToHtml() {
		$this->_prepareData();
		return parent::_beforeToHtml();
	}

	/**
	 * Get collection items
	 *
	 * @return Collection
	 */
	public function getItems() {
		/**
		 * getIdentities() depends on _itemCollection populated, but it can be empty if the block is hidden
		 * @see https://github.com/magento/magento2/issues/5897
		 */
		if ($this->_itemCollection === null) {
			$this->_prepareData();
		}
		return $this->_itemCollection;
	}

	/**
	 * Return identifiers for produced content
	 *
	 * @return array
	 */
	public function getIdentities() {
		$identities = [];
		foreach ($this->getItems() as $item) {
			// phpcs:ignore Magento2.Performance.ForeachArrayMerge
			$identities = array_merge($identities, $item->getIdentities());
		}
		return $identities;
	}

	/**
	 * Find out if some products can be easy added to cart
	 *
	 * @return bool
	 */
	public function canItemsAddToCart() {
		foreach ($this->getItems() as $item) {
			if (!$item->isComposite() && $item->isSaleable() && !$item->getRequiredOptions()) {
				return true;
			}
		}
		return false;
	}

}