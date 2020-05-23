<?php declare (strict_types = 1);

namespace Dolphin\DynamicRules\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface DynamicRulesRepositoryInterface {

	/**
	 * Save DynamicRules
	 * @param  string $Id
	 * @param  string $source_category
	 * @param  string $source_attributes
	 * @param  string $products
	 * @param  string $weight
	 * @return \Dolphin\DynamicRules\Api\Data\DynamicRulesInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function update(
		$Id,
		$source_category,
		$source_attributes,
		$products,
		$weight
	);

	/**
	 * Save DynamicRules
	 * @param  string $source_category
	 * @param  string $source_attributes
	 * @param  string $products
	 * @param  string $weight
	 * @return \Dolphin\DynamicRules\Api\Data\DynamicRulesInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function save(
		$source_category,
		$source_attributes,
		$products,
		$weight
	);

	/**
	 * Retrieve DynamicRules
	 * @param string $Id
	 * @return \Dolphin\DynamicRules\Api\Data\DynamicRulesInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function get($Id);

	/**
	 * Retrieve DynamicRules matching the specified criteria.
	 * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
	 * @return \Dolphin\DynamicRules\Api\Data\DynamicRulesSearchResultsInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getList(
		\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
	);

	/**
	 * Delete DynamicRules
	 * @param \Dolphin\DynamicRules\Api\Data\DynamicRulesInterface $dynamicRules
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function delete(
		\Dolphin\DynamicRules\Api\Data\DynamicRulesInterface $dynamicRules
	);

	/**
	 * Delete DynamicRules by ID
	 * @param string $Id
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function deleteById($Id);
}
