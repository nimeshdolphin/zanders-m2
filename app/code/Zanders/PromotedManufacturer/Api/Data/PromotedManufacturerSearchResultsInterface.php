<?php declare(strict_types=1);


namespace Zanders\PromotedManufacturer\Api\Data;


interface PromotedManufacturerSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get PromotedManufacturer list.
     * @return \Zanders\PromotedManufacturer\Api\Data\PromotedManufacturerInterface[]
     */
    public function getItems();

    /**
     * Set category_id list.
     * @param \Zanders\PromotedManufacturer\Api\Data\PromotedManufacturerInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

