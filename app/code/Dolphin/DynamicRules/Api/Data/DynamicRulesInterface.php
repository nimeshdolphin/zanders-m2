<?php declare(strict_types=1);


namespace Dolphin\DynamicRules\Api\Data;

interface DynamicRulesInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const SOURCE_ATTRIBUTES = 'source_attributes';
    const PRODUCTS = 'products';
    const WEIGHT = 'weight';
    const SOURCE_CATEGORY = 'source_category';
    const ID = 'id';

    /**
     * Get ID
     * @return string|null
     */
    public function getId();

    /**
     * Set ID
     * @param string $dynamicrulesId
     * @return \Dolphin\DynamicRules\Api\Data\DynamicRulesInterface
     */
    public function setId($dynamicrulesId);

    /**
     * Get source_category
     * @return string|null
     */
    public function getSourceCategory();

    /**
     * Set source_category
     * @param string $sourceCategory
     * @return \Dolphin\DynamicRules\Api\Data\DynamicRulesInterface
     */
    public function setSourceCategory($sourceCategory);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Dolphin\DynamicRules\Api\Data\DynamicRulesExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Dolphin\DynamicRules\Api\Data\DynamicRulesExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Dolphin\DynamicRules\Api\Data\DynamicRulesExtensionInterface $extensionAttributes
    );

    /**
     * Get source_attributes
     * @return string|null
     */
    public function getSourceAttributes();

    /**
     * Set source_attributes
     * @param string $sourceAttributes
     * @return \Dolphin\DynamicRules\Api\Data\DynamicRulesInterface
     */
    public function setSourceAttributes($sourceAttributes);

    /**
     * Get products
     * @return string|null
     */
    public function getProducts();

    /**
     * Set products
     * @param string $products
     * @return \Dolphin\DynamicRules\Api\Data\DynamicRulesInterface
     */
    public function setProducts($products);

    /**
     * Get weight
     * @return string|null
     */
    public function getWeight();

    /**
     * Set weight
     * @param string $weight
     * @return \Dolphin\DynamicRules\Api\Data\DynamicRulesInterface
     */
    public function setWeight($weight);
}
