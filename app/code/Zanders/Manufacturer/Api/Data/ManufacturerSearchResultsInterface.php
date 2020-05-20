<?php
/**
 * @category   Zanders
 * @package    Zanders_Manufacturer
 */

namespace Zanders\Manufacturer\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for Manufacturer search results.
 * @api
 */
interface ManufacturerSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get Manufacturers list.
     *
     * @return \Zanders\Manufacturer\Api\Data\ManufacturerInterface[]
     */
    public function getItems();

    /**
     * Set Manufacturers list.
     *
     * @param \Zanders\Manufacturer\Api\Data\ManufacturerInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
