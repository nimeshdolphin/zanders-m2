<?php
/**
 * @category   Zanders
 * @package    Zanders_FlatRate
 */

namespace Zanders\FlatRate\Model\Flatrate\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;
use Zanders\FlatRate\Model\FlatRate;

/**
 * Customer flat_rate_tier source model.
 */
class Customer extends AbstractSource implements SourceInterface, OptionSourceInterface
{
    /**
     * @var FlatRate
     */
    protected $_flatRate;

    /**
     * Initialize dependencies.
     *
     * @param FlatRate $flatRate
     */
    public function __construct(
        FlatRate $flatRate
    )
    {
        $this->_flatRate = $flatRate;
    }

    /**
     * Retrieve all flat_rate options.
     *
     * @param bool $withEmpty
     * @return array
     */
    public function getAllOptions($withEmpty = true)
    {
        $options = [];

        $flatRates = $this->_flatRate->getCollection();
        foreach ($flatRates as $flatrate) {
            $options[] = [
                'value' => $flatrate->getId(),
                'label' => $flatrate->getName(),
            ];
        }

        if ($withEmpty) {
            if (empty($options)) {
                return [['value' => '', 'label' => __('')]];
            } else {
                return array_merge([['value' => '', 'label' => __('')]], $options);
            }
        }
        return $options;
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
