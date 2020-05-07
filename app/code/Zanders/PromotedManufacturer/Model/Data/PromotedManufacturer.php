<?php declare(strict_types=1);


namespace Zanders\PromotedManufacturer\Model\Data;

use Zanders\PromotedManufacturer\Api\Data\PromotedManufacturerInterface;


class PromotedManufacturer extends \Magento\Framework\Api\AbstractExtensibleObject implements PromotedManufacturerInterface
{

    /**
     * Get id
     * @return string|null
     */
    public function getPromotedmanufacturerId()
    {
        return $this->_get(self::id);
    }

    /**
     * Set id
     * @param string $promotedmanufacturerId
     * @return \Zanders\PromotedManufacturer\Api\Data\PromotedManufacturerInterface
     */
    public function setPromotedmanufacturerId($promotedmanufacturerId)
    {
        return $this->setData(self::id, $promotedmanufacturerId);
    }

    /**
     * Get category_id
     * @return string|null
     */
    public function getCategoryId()
    {
        return $this->_get(self::CATEGORY_ID);
    }

    /**
     * Set category_id
     * @param string $categoryId
     * @return \Zanders\PromotedManufacturer\Api\Data\PromotedManufacturerInterface
     */
    public function setCategoryId($categoryId)
    {
        return $this->setData(self::CATEGORY_ID, $categoryId);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Zanders\PromotedManufacturer\Api\Data\PromotedManufacturerExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Zanders\PromotedManufacturer\Api\Data\PromotedManufacturerExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Zanders\PromotedManufacturer\Api\Data\PromotedManufacturerExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get manufacturer_id
     * @return string|null
     */
    public function getManufacturerId()
    {
        return $this->_get(self::MANUFACTURER_ID);
    }

    /**
     * Set manufacturer_id
     * @param string $manufacturerId
     * @return \Zanders\PromotedManufacturer\Api\Data\PromotedManufacturerInterface
     */
    public function setManufacturerId($manufacturerId)
    {
        return $this->setData(self::MANUFACTURER_ID, $manufacturerId);
    }
}

