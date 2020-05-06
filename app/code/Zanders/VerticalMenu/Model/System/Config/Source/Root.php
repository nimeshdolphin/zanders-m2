<?php
namespace Zanders\VerticalMenu\Model\System\Config\Source;

class Root implements \Magento\Framework\Option\ArrayInterface
{
	public function toOptionArray()
	{
		$options = array(
			array(
				'label' => __('Store base'),
				'value' => 'root',
			),
			array(
				'label' => __('Current category children'),
				'value' => 'current',
			),
			array(
				'label' => __('Same level as current category'),
				'value' => 'siblings',
			),
			array(
				'label' => __('Category Level 2'),
				'value' => '2',
			),
		);
		return $options;
	}
}