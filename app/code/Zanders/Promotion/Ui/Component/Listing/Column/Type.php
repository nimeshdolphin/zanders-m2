<?php
/**
 * @category   Zanders
 * @package    Zanders_Promotion
 */

namespace Zanders\Promotion\Ui\Component\Listing\Column;

class Type extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var \Zanders\Promotion\Model\Config\Source\Type
     */
    protected $configPromotionType;

    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Zanders\Promotion\Model\Config\Source\Type $configPromotionType,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->configPromotionType = $configPromotionType;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        $promotionTypeOptions = $this->configPromotionType->toOptionArray();
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                foreach ($promotionTypeOptions as $promotionTypeOption) {
                    if ($promotionTypeOption['value'] == $item['amount_type']) {
                        $item['amount_type'] = $promotionTypeOption['label'];
                        break;
                    }
                }
            }
        }

        return $dataSource;
    }
}
