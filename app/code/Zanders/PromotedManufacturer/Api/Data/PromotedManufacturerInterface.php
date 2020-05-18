<?php declare(strict_types=1);


namespace Zanders\PromotedManufacturer\Api\Data;


interface PromotedManufacturerInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const id = 'id';
    const CATEGORY_ID = 'category_id';
    const MANUFACTURER_ID = 'manufacturer_id';

    /**
     * Get id
     * @return string|null
     */
    public function getId();

    /**
     * Set id
     * @param string $promotedmanufacturerId
     * @return \Zanders\PromotedManufacturer\Api\Data\PromotedManufacturerInterface
     */
    public function setId($promotedmanufacturerId);

    /**
     * Get category_id
     * @return string|null
     */
    public function getCategoryId();

    /**
     * Set category_id
     * @param string $categoryId
     * @return \Zanders\PromotedManufacturer\Api\Data\PromotedManufacturerInterface
     */
    public function setCategoryId($categoryId);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Zanders\PromotedManufacturer\Api\Data\PromotedManufacturerExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Zanders\PromotedManufacturer\Api\Data\PromotedManufacturerExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Zanders\PromotedManufacturer\Api\Data\PromotedManufacturerExtensionInterface $extensionAttributes
    );

    /**
     * Get manufacturer_id
     * @return string|null
     */
    public function getManufacturerId();

    /**
     * Set manufacturer_id
     * @param string $manufacturerId
     * @return \Zanders\PromotedManufacturer\Api\Data\PromotedManufacturerInterface
     */
    public function setManufacturerId($manufacturerId);
}

