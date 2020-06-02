<?php
/**
 * @category   Zanders
 * @package    Zanders_Manufacturer
 */

namespace Zanders\Manufacturer\Api;

/**
 * Manufacturer CRUD interface.
 * @api
 */
interface ManufacturerRepositoryInterface {


	/**
	 * Save Manufacturer.
	 * @param  string $name
	 * @param  string $address
	 * @param  string $serialized_display_on
	 * @param  string $serialized_text
	 * @param  string $phone
	 * @param  string $web
	 * @param  string $enable
	 * @param  string $image
	 * @param  string $imagetype
	 * @return \Zanders\Manufacturer\Api\Data\ManufacturerInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function save($name, $address, $serialized_display_on, $serialized_text, $phone, $web, $enable, $image, $image_type);

	/**
	 * Retrieve Manufacturer.
	 *
	 * @param int $manufacturerId
	 * @return string
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getById($manufacturerId);

	/**
	 * Retrieve Manufacturer matching the specified criteria.
	 *
	 * @return \Zanders\Manufacturer\Api\Data\ManufacturerSearchResultsInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getList();

	/**
	 * Delete Manufacturer.
	 *
	 * @param \Zanders\Manufacturer\Api\Data\ManufacturerInterface $manufacturer
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function delete(\Zanders\Manufacturer\Api\Data\ManufacturerInterface $manufacturer);

	/**
	 * Delete Manufacturer by ID.
	 *
	 * @param int $manufacturerId
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function deleteById($manufacturerId);


	/**
	 * Retrieve Manufacturer matching the specified criteria.
	 *
	 * @return string
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getManufacturerList();


	/**
	 * Retrieve Manufacturer.
	 *
	 * @param int $manufacturerId
	 * @return string
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getManufactureById($manufacturerId);


	/**
	 * Save Manufacturer.
	 * @param  string $id
	 * @param  string $name
	 * @param  string $address
	 * @param  string $serialized_display_on
	 * @param  string $serialized_text
	 * @param  string $phone
	 * @param  string $web
	 * @param  string $enable
	 * @param  string $image
	 * @param  string $imagetype
	 * @return \Zanders\Manufacturer\Api\Data\ManufacturerInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function updateManufacture($id, $name, $address, $serialized_display_on, $serialized_text, $phone, $web, $enable, $image, $image_type);

	/**
	 * Save Manufacturer.
	 * @param  string $name
	 * @param  string $address
	 * @param  string $serialized_display_on
	 * @param  string $serialized_text
	 * @param  string $phone
	 * @param  string $web
	 * @param  string $enable
	 * @param  string $image
	 * @param  string $imagetype
	 * @return \Zanders\Manufacturer\Api\Data\ManufacturerInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function saveManufacture($name, $address, $serialized_display_on, $serialized_text, $phone, $web, $enable, $image, $image_type);

}
