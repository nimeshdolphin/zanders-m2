<?php
/**
 * @category   Zanders
 * @package    Zanders_Sports
 */

namespace Zanders\Sports\Model\Flatrate\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\DB\Ddl\Table;
use Zanders\Manufacturer\Model\Manufacturer;

/**
 * Customer flat_rate_tier source model.
 */
class Customer extends AbstractSource implements SourceInterface, OptionSourceInterface
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
     * Retrieve all flat_rate options.
     *
     * @param bool $withEmpty
     * @return array
     */
    public function getAllOptions($withEmpty = true)
    {
        $this->_options = [
            [
                'value' => 1,
                'label' => 'Default Rate',
            ],
            [
                'value' => 2,
                'label' => 'GearFire',
            ],
            [
                'value' => 3,
                'label' => 'ClassicFirearms',
            ],
            [
                'value' => 4,
                'label' => 'GunPro',
            ]
        ];

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
}
