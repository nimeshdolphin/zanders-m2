<?php declare (strict_types = 1);

namespace Dolphin\DynamicRules\Helper;

use Dolphin\DynamicRules\Model\ResourceModel\DynamicRules\CollectionFactory as DynamicRulesCollectionFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Zanders\PromotedManufacturer\Model\ResourceModel\PromotedManufacturer\CollectionFactory as PromotedManufacturerCollectionFactory;

class Data extends AbstractHelper {

	protected $_productCollectionFactory;

	/**
	 * @var $_registry
	 */
	protected $_registry;

	protected $_productRepository;

	protected $_productloader;

	/**
	 * @param \Magento\Framework\App\Helper\Context $context
	 */
	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\Registry $registry,
		\Magento\Catalog\Model\ProductRepository $productRepository,
		DynamicRulesCollectionFactory $dynamicRulesCollectionFactory,
		\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
		PromotedManufacturerCollectionFactory $promotedManufacturerCollectionFactory,
		\Magento\Catalog\Model\ProductFactory $_productloader

	) {
		parent::__construct($context);
		$this->_registry = $registry;
		$this->_productRepository = $productRepository;
		$this->dynamicRulesCollectionFactory = $dynamicRulesCollectionFactory;
		$this->_productCollectionFactory = $productCollectionFactory;
		$this->promotedManufacturerCollectionFactory = $promotedManufacturerCollectionFactory;
		$this->_productloader = $_productloader;
	}

	public function getCurrentProduct() {
		return $this->_registry->registry('current_product');
	}

	public function dynamicProducts() {
		$productId = $this->getCurrentProduct()->getId();
		$product = $this->_productRepository->getById($productId);
		$category = $product->getCategoryIds();

		$jsonData = array();
		if (count($category) > 0) {
			$collection = $this->dynamicRulesCollectionFactory->create()
				->addFieldToFilter('source_category', array('in' => $category))
				->setOrder('weight', 'asc');

			//echo $collection->getSelect();exit;
			foreach ($collection as $data) {
				if ($data->getData('source_attributes')) {
					$result = str_replace("'", '"', $data->getData('source_attributes'));
					if ($this->is_json($result)) {
						$jsonData[$data->getData('id')] = json_decode($result, true);
						$ruleData[$data->getData('id')]['products'] = $data->getData('products');
						$ruleData[$data->getData('id')]['weight'] = $data->getData('weight');
						$ruleData[$data->getData('id')]['source_category'] = $data->getData('source_category');
					}
				}
			}
		}

		$dynamicRecords = array();
		foreach ($jsonData as $ruleid => $attributes) {

			$exist = array();
			foreach ($attributes as $key => $value) {

				if (in_array($product->getData($key), $value) or in_array($product->getAttributeText($key), $value)) {
					$exist[] = '1';
				} else {
					$exist[] = '0';
				}
			}
			if (!in_array('0', $exist)) {
				$dynamicRecords[] = $ruleid;
				$exist = array();
			}
		}

		$products = '';
		foreach ($dynamicRecords as $id) {
			$data = $ruleData[$id];
			$products = $products . ',' . $data['products'];
			$categories[] = $data['source_category'];

		}

		$products = array_filter(explode(',', $products));
		$spoProducts = $this->getPromoted($products); /* Matching Products in Dynamic Rule */

		$ids = array();
		$pro = array();
		foreach ($products as $id) {
			if (!in_array($id, $spoProducts)) {
				$pro[] = $id;
			}
		}

		$spoProducts = array();
		foreach ($pro as $id) {
			$spoProducts[] = $id;
		}
		//print_r($spoProducts);exit;
		return $spoProducts;
	}

	public function is_json($string, $return_data = false) {
		$data = json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE) ? ($return_data ? $data : TRUE) : FALSE;
	}

	private function getPromoted($products) {

		$collection = $this->_productCollectionFactory->create();
		$collection->addAttributeToSelect('*');
		$collection->addFieldToFilter('entity_id', ['in' => $products]);

		$categories = array();
		foreach ($collection as $cate) {
			//print_r($cate->getCategoryIds()); exit;
			foreach ($cate->getCategoryIds() as $id) {
				$categories[] = $id;
			}
		}

		$categoriesF = array_unique(array_filter($categories));

		if (count($categories)) {

			$promoted = $this->promotedManufacturerCollectionFactory->create()
				->addFieldToFilter('category_id', array('in' => $categories));

			if ($promoted->count()) {
				foreach ($promoted as $data) {

					$data1 = explode(',', $data['manufacturer_id']);
					foreach ($data1 as $id) {
						$man_ids[] = $id;
					}
				}
			}

			foreach ($products as $id) {
				//$product = Mage::getModel('catalog/product')->load($id);
				$product = $this->_productloader->create()->load($id);
				if (in_array($product->getData('manufacturer'), $man_ids)) {
					//$spo[] = $product->getId();
					$s[$product->getId()] = $product->getData('manufacturer');
				}
			}

			//$man_ids = array_reverse($man_ids);

			foreach ($man_ids as $p) {
				foreach ($s as $key => $value) {
					if ($p == $value) {
						$spo[] = $key;
					}
				}
			}

			//echo "<pre>"; print_r($man_ids); print_r($s); print_r($spo);	//exit;
			return $spo;

		}

	}

}
