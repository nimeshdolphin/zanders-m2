<?php
/**
 * @category   Zanders
 * @package    Zanders_Eshow
 */

namespace Zanders\Eshow\Ui\Component\Listing\Column;

class Type extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var \Zanders\Eshow\Model\Config\Source\Type
     */
    protected $configEshowType;

    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Zanders\Eshow\Model\Config\Source\ShowType $configEshowType,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->configEshowType = $configEshowType;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        $eshowTypeOptions = $this->configEshowType->toOptionArray();
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                foreach ($eshowTypeOptions as $eshowTypeOption) {
                    if ($eshowTypeOption['value'] == $item['amount_type']) {
                        $item['amount_type'] = $eshowTypeOption['label'];
                        break;
                    }
                }
            }
        }

        return $dataSource;
    }
}
