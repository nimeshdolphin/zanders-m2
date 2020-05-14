<?php
/**
 * @category   Zanders
 * @package    Zanders_Eshow
 */

namespace Zanders\Eshow\Model\Config\Source;

class ShowType implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Item Type
     */
    const SHOW_TYPE_FREE = 'free';
    const SHOW_TYPE_PERCENT = 'percent';
    const SHOW_TYPE_PRICE = 'price';
    const SHOW_TYPE_MARKDOWN = 'markdown';

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::SHOW_TYPE_FREE, 'label' => __('Buy Some Get Free')],
            ['value' => self::SHOW_TYPE_PERCENT, 'label' => __('Buy Some Get Percent Off')],
            ['value' => self::SHOW_TYPE_PRICE, 'label' => __('Special Pricing')],
            ['value' => self::SHOW_TYPE_MARKDOWN, 'label' => __('Markdown Pricing')]
        ];
    }
}
