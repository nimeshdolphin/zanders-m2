<?php declare(strict_types=1);


namespace Dolphin\DynamicRules\Api\Data;


interface DynamicRulesSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get DynamicRules list.
     * @return \Dolphin\DynamicRules\Api\Data\DynamicRulesInterface[]
     */
    public function getItems();

    /**
     * Set source_category list.
     * @param \Dolphin\DynamicRules\Api\Data\DynamicRulesInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

