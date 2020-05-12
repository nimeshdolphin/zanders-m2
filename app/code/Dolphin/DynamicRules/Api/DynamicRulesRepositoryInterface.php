<?php declare(strict_types=1);


namespace Dolphin\DynamicRules\Api;

use Magento\Framework\Api\SearchCriteriaInterface;


interface DynamicRulesRepositoryInterface
{

    /**
     * Save DynamicRules
     * @param \Dolphin\DynamicRules\Api\Data\DynamicRulesInterface $dynamicRules
     * @return \Dolphin\DynamicRules\Api\Data\DynamicRulesInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Dolphin\DynamicRules\Api\Data\DynamicRulesInterface $dynamicRules
    );

    /**
     * Retrieve DynamicRules
     * @param string $dynamicrulesId
     * @return \Dolphin\DynamicRules\Api\Data\DynamicRulesInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($dynamicrulesId);

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
     * @param string $dynamicrulesId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($dynamicrulesId);
}

