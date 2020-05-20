<?php
/**
 * @category   Zanders
 * @package    Zanders_Manufacturer
 */

namespace Zanders\Manufacturer\Model\Manufacturer\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\DB\Ddl\Table;
use Zanders\Manufacturer\Model\Manufacturer;

/**
 * Product manufacturer source model.
 */
class Eshow implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Zanders\Manufacturer\Api\ManufacturerRepositoryInterface
     */
    protected $_manufacturerClassRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $_searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    protected $_filterBuilder;

    /**
     * @var \Magento\Framework\Api\SortOrderBuilder
     */
    protected $_sortOrderBuilder;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Magento\Framework\Api\SortOrderBuilder $sortOrderBuilder
     * @param \Zanders\Manufacturer\Api\ManufacturerRepositoryInterface $manufacturerClassRepository
     */
    public function __construct(
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\Api\SortOrderBuilder $sortOrderBuilder,
        \Zanders\Manufacturer\Api\ManufacturerRepositoryInterface $manufacturerClassRepository
    )
    {
        $this->_filterBuilder = $filterBuilder;
        $this->_sortOrderBuilder = $sortOrderBuilder;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_manufacturerClassRepository = $manufacturerClassRepository;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $filter = $this->_filterBuilder
            ->setField(Manufacturer::STATUS)
            ->setValue(\Zanders\Manufacturer\Model\Source\Status::STATUS_ENABLED)
            ->create();

        $sortOrder = $this->_sortOrderBuilder
            ->setField(Manufacturer::NAME)
            ->setDirection(\Magento\Framework\Api\SortOrder::SORT_ASC)
            ->create();

        $searchCriteria = $this->_searchCriteriaBuilder->addFilters([$filter])->addSortOrder($sortOrder)->create();
        $searchResults = $this->_manufacturerClassRepository->getList($searchCriteria);

        $options = [];
        foreach ($searchResults->getItems() as $manufacturer) {
            $options[] = [
                'value' => $manufacturer->getId(),
                'label' => $manufacturer->getName(),
            ];
        }
        return $options;
    }
}
