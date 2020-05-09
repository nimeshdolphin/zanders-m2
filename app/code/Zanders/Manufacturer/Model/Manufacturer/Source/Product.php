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
class Product extends AbstractSource implements SourceInterface, OptionSourceInterface
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
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory
     */
    protected $_optionFactory;

    /**
     * Initialize dependencies.
     *
     * @param \Zanders\Manufacturer\Model\ResourceModel\Manufacturer\CollectionFactory $classesFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $optionFactory
     * @param \Zanders\Manufacturer\Api\ManufacturerRepositoryInterface $manufacturerClassRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Magento\Framework\Api\SortOrderBuilder $sortOrderBuilder
     */
    public function __construct(
        \Zanders\Manufacturer\Model\ResourceModel\Manufacturer\CollectionFactory $classesFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $optionFactory,
        \Zanders\Manufacturer\Api\ManufacturerRepositoryInterface $manufacturerClassRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\Api\SortOrderBuilder $sortOrderBuilder
    )
    {
        $this->_classesFactory = $classesFactory;
        $this->_optionFactory = $optionFactory;
        $this->_manufacturerClassRepository = $manufacturerClassRepository;
        $this->_filterBuilder = $filterBuilder;
        $this->_sortOrderBuilder = $sortOrderBuilder;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Retrieve all manufacturer options.
     *
     * @param bool $withEmpty
     * @return array
     */
    public function getAllOptions($withEmpty = true)
    {
        if (!$this->_options) {
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

            foreach ($searchResults->getItems() as $manufacturer) {
                $this->_options[] = [
                    'value' => $manufacturer->getId(),
                    'label' => $manufacturer->getName(),
                ];
            }
        }

        if ($withEmpty) {
            if (!$this->_options) {
                return [['value' => '', 'label' => __('')]];
            } else {
                return array_merge([['value' => '', 'label' => __('')]], $this->_options);
            }
        }
        return $this->_options;
    }

    /**
     * Get a text for option value
     *
     * @param string|integer $value
     * @return string
     */
    public function getOptionText($value)
    {
        $options = $this->getAllOptions();

        foreach ($options as $item) {
            if ($item['value'] == $value) {
                return $item['label'];
            }
        }
        return false;
    }

    /**
     * Retrieve flat column definition
     *
     * @return array
     */
    public function getFlatColumns()
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();

        return [
            $attributeCode => [
                'default' => null,
                'extra' => null,
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => $attributeCode . ' column',
            ],
        ];
    }

    /**
     * Retrieve Select for update attribute value in flat table
     *
     * @param int $store
     * @return  \Magento\Framework\DB\Select|null
     */
    public function getFlatUpdateSelect($store)
    {
        /** @var $option \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option */
        $option = $this->_optionFactory->create();
        return $option->getFlatUpdateSelect($this->getAttribute(), $store, false);
    }
}
