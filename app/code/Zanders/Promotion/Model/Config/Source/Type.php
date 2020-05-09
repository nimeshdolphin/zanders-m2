<?php
/**
 * @category   Zanders
 * @package    Zanders_Promotion
 */

namespace Zanders\Promotion\Model\Config\Source;

class Type implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Item Type
     */
    const TYPE_GENERAL = 0;
    const TYPE_DEALER = 1;
    const TYPE_CONSUMER = 2;

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::TYPE_GENERAL, 'label' => __('General')],
            ['value' => self::TYPE_DEALER, 'label' => __('Dealer')],
            ['value' => self::TYPE_CONSUMER, 'label' => __('Consumer')]
        ];
    }
}
