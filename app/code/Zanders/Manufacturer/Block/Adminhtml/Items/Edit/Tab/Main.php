<?php
/**
 * @category   Zanders
 * @package    Zanders_Manufacturer
 */

namespace Zanders\Manufacturer\Block\Adminhtml\Items\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class Main extends Generic implements TabInterface
{
    protected $_wysiwygConfig;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    )
    {
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Manufacturer Information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Manufacturer Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_zanders_manufacturer_items');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('item_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Manufacturer Information')]);
        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }
        $fieldset->addField(
            'name',
            'text',
            ['name' => 'name', 'label' => __('Name'), 'title' => __('Name'), 'required' => true]
        );
        $fieldset->addField(
            'address',
            'textarea',
            ['name' => 'address', 'label' => __('Address'), 'title' => __('Address'), 'required' => false]
        );
        $fieldset->addField(
            'serialized_display_on',
            'select',
            ['name' => 'serialized_display_on', 'label' => __('Serialized Display On'), 'title' => __('Serialized Display On'), 'options' => ['' => __('--Please Select--'), 'serialized_yes' => __('Show on serialized items'), 'serialized_no' => __('Show on non-serialized items'), 'all' => __('Show on all items')], 'required' => false]
        );
        $fieldset->addField(
            'serialized_text',
            'editor',
            [
                'name' => 'serialized_text',
                'label' => __('Serialized Text'),
                'title' => __('Serialized Text'),
                'style' => 'height:15em;',
                'required' => false,
                'config' => $this->_wysiwygConfig->getConfig(),
                'wysiwyg' => true
            ]
        );
        $fieldset->addField(
            'phone',
            'text',
            ['name' => 'phone', 'label' => __('Phone'), 'title' => __('Phone'), 'required' => false]
        );
        $fieldset->addField(
            'web',
            'text',
            ['name' => 'web', 'label' => __('Website'), 'title' => __('Website'), 'required' => false]
        );

        $fieldset->addField(
            'image',
            'image',
            [
                'name' => 'image',
                'label' => __('Image'),
                'title' => __('Image'),
                'required' => false
            ]
        );
        $fieldset->addField(
            'enable',
            'select',
            ['name' => 'enable', 'label' => __('Status'), 'title' => __('Status'), 'options' => [0 => 'Disable', 1 => 'Enable'], 'required' => true]
        );

        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
