<?php declare (strict_types = 1);

namespace Zanders\PromotedManufacturer\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface PromotedManufacturerRepositoryInterface {

	/**
	 * update PromotedManufacturer
	 * @param string id
	 * @param string $category_id
	 * @param string $manufacture_id
	 * @return \Zanders\PromotedManufacturer\Api\Data\PromotedManufacturerInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function update($id,
		$category_id,
		$manufacture_id
	);

	/**
	 * Save PromotedManufacturer
	 * @param string $category_id
	 * @param string $manufacture_id
	 * @return \Zanders\PromotedManufacturer\Api\Data\PromotedManufacturerInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function save($category_id,
		$manufacture_id
	);

	/**
	 * Retrieve PromotedManufacturer
	 * @param string $id
	 * @return \Zanders\PromotedManufacturer\Api\Data\PromotedManufacturerInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function get($id);

	/**
	 * Retrieve PromotedManufacturer matching the specified criteria.
	 * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
	 * @return \Zanders\PromotedManufacturer\Api\Data\PromotedManufacturerSearchResultsInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getList(
		\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
	);

	/**
	 * Delete PromotedManufacturer
	 * @param \Zanders\PromotedManufacturer\Api\Data\PromotedManufacturerInterface $promotedManufacturer
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function delete(
		\Zanders\PromotedManufacturer\Api\Data\PromotedManufacturerInterface $promotedManufacturer
	);

	/**
	 * Delete PromotedManufacturer by ID
	 * @param string $promotedmanufacturerId
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function deleteById($promotedmanufacturerId);

	/**
	 * Retrieve PromotedManufacturer matching the specified criteria.
	 * @return \Zanders\PromotedManufacturer\Api\Data\PromotedManufacturerSearchResultsInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function list();

}
