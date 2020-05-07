<?php
/**
 * @category   Zanders
 * @package    Zanders_CategoryCode
 */

namespace Zanders\CategoryCode\Block\Adminhtml\Items\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('zanders_categorycode_items_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Category Code'));
    }
}
