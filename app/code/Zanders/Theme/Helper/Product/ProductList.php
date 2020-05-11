<?php
namespace Zanders\Theme\Helper\Product;

use Magento\Catalog\Helper\Product\ProductList as ProductListHelper;

class ProductList extends ProductListHelper
{
    public function getAvailableViewMode()
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_LIST_MODE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        switch ($value) {
            case 'grid':
                $availableMode = ['grid' => __('Grid')];
                break;

            case 'list':
                $availableMode = ['list' => __('List')];
                break;

            case 'grid-list':
                $availableMode = ['grid' => __('Grid'), 'list' =>  __('List') , 'simple' =>  __('Simple')];
                break;

            case 'list-grid':
                $availableMode = ['list' => __('List'), 'grid' => __('Grid')];
                break;
            default:
                $availableMode = null;
                break;
        }
        return $availableMode;
    }
}