<?php
/**
 * @category   Zanders
 * @package    Zanders_Manufacturer
 */

namespace Zanders\Manufacturer\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Manufacturer CRUD interface.
 * @api
 */
interface ManufacturerRepositoryInterface
{
    /**
     * Save Manufacturer.
     *
     * @param \Zanders\Manufacturer\Api\Data\ManufacturerInterface $manufacturer
     * @return \Zanders\Manufacturer\Api\Data\ManufacturerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Zanders\Manufacturer\Api\Data\ManufacturerInterface $manufacturer);

    /**
     * Retrieve Manufacturer.
     *
     * @param int $manufacturerId
     * @return \Zanders\Manufacturer\Api\Data\ManufacturerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($manufacturerId);

    /**
     * Retrieve Manufacturer matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Zanders\Manufacturer\Api\Data\ManufacturerSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

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
}
