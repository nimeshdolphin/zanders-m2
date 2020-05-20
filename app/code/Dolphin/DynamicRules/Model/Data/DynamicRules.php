<?php declare(strict_types=1);


namespace Dolphin\DynamicRules\Model\Data;

use Dolphin\DynamicRules\Api\Data\DynamicRulesInterface;

class DynamicRules extends \Magento\Framework\Api\AbstractExtensibleObject implements DynamicRulesInterface
{

    /**
     * Get id
     * @return string|null
     */
    public function getId()
    {
        return $this->_get(self::id);
    }

    /**
     * Set id
     * @param string $dynamicrulesId
     * @return \Dolphin\DynamicRules\Api\Data\DynamicRulesInterface
     */
    public function setId($dynamicrulesId)
    {
        return $this->setData(self::id, $dynamicrulesId);
    }

    /**
     * Get source_category
     * @return string|null
     */
    public function getSourceCategory()
    {
        return $this->_get(self::SOURCE_CATEGORY);
    }

    /**
     * Set source_category
     * @param string $sourceCategory
     * @return \Dolphin\DynamicRules\Api\Data\DynamicRulesInterface
     */
    public function setSourceCategory($sourceCategory)
    {
        return $this->setData(self::SOURCE_CATEGORY, $sourceCategory);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Dolphin\DynamicRules\Api\Data\DynamicRulesExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Dolphin\DynamicRules\Api\Data\DynamicRulesExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Dolphin\DynamicRules\Api\Data\DynamicRulesExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get source_attributes
     * @return string|null
     */
    public function getSourceAttributes()
    {
        return $this->_get(self::SOURCE_ATTRIBUTES);
    }

    /**
     * Set source_attributes
     * @param string $sourceAttributes
     * @return \Dolphin\DynamicRules\Api\Data\DynamicRulesInterface
     */
    public function setSourceAttributes($sourceAttributes)
    {
        return $this->setData(self::SOURCE_ATTRIBUTES, $sourceAttributes);
    }

    /**
     * Get products
     * @return string|null
     */
    public function getProducts()
    {
        return $this->_get(self::PRODUCTS);
    }

    /**
     * Set products
     * @param string $products
     * @return \Dolphin\DynamicRules\Api\Data\DynamicRulesInterface
     */
    public function setProducts($products)
    {
        return $this->setData(self::PRODUCTS, $products);
    }

    /**
     * Get weight
     * @return string|null
     */
    public function getWeight()
    {
        return $this->_get(self::WEIGHT);
    }

    /**
     * Set weight
     * @param string $weight
     * @return \Dolphin\DynamicRules\Api\Data\DynamicRulesInterface
     */
    public function setWeight($weight)
    {
        return $this->setData(self::WEIGHT, $weight);
    }
}
