<?php
namespace Zanders\VerticalMenu\Block;

class Menu extends \Magento\Framework\View\Element\Template
{
    protected $_categoryHelper;
    protected $_categoryFactory;
    protected $_storeManager;
    protected $scopeConfig;
	private $layerResolver;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
		\Magento\Catalog\Model\Layer\Resolver $layerResolver,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {

        $this->_categoryHelper = $categoryHelper;
        $this->_categoryFactory = $categoryFactory;
        $this->_storeManager = $context->getStoreManager();
		$this->layerResolver = $layerResolver;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }
	
	public function drawOpenCategoryItem( $category, $level = 0, array $levelClass = NULL )
    {
        $html = array ();

        if( !$category->getIsActive() ) {
            return '';
        }
        if( !$category->getIncludeInMenu() ) {
            return '';
        }

        if( !isset( $levelClass ) ) {
            $levelClass = array ();
        }
        $combineClasses = array ();

        $combineClasses[ ] = 'level' . $level;
        if( $this->_isCurrentCategory( $category ) ) {
            $combineClasses[ ] = 'active';
        } else {
            $combineClasses[ ] = $this->isCategoryActive( $category ) ? 'parent' : 'inactive';
        }
        $levelClass[ ] = implode( '-', $combineClasses );

        if($category->hasChildren()) {
        	$levelClass[ ] = 'has-children';
        }

        $levelClass = array_merge( $levelClass, $combineClasses );

        $levelClass[ ] = $this->_getClassNameFromCategoryName( $category );

        $productCount = '';
        if( $this->displayProductCount() ) {
			$catalogLayer = $this->layerResolver->get();
			$n  = $catalogLayer->setCurrentCategory($category->getID())->getProductCollection()->getSize();
            $productCount = '<span class="product-count"> (' . $n . ')</span>';
        }

        // indent HTML!
        $html[ 1 ] = str_pad( "", ( ( $level * 2 ) + 4 ), " " ) . '<span class="vertnav-cat"><a href="' . $this->_categoryHelper->getCategoryUrl( $category ) . '"><span>' . $category->getName() . '</span></a>' . $productCount . "</span>\n";

        $autoMaxDepth = $this->scopeConfig->getValue('catalog/vertnav/expand_all_max_depth', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $autoExpand   = $this->scopeConfig->getValue('catalog/vertnav/expand_all', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        if( in_array( $category->getId(), $this->getCurrentCategoryPath() ) || ( $autoExpand && $autoMaxDepth == 0 ) || ( $autoExpand && $autoMaxDepth > $level + 1 )
        ) {
			$subcat = $this->getCategoryModel($category->getId());
            $children = $this->_getCategoryCollection()->addIdFilter( $subcat->getChildren() );
            $children = $this->toLinearArray( $children );
			$hasChildren = $children && ( $childrenCount = count( $children ) );
            if( $hasChildren ) {
                $children     = $this->toLinearArray( $children );
                $htmlChildren = '';

                foreach( $children as $i => $child ) {
                    $class = array ();
                    if( $childrenCount == 1 ) {
                        $class[ ] = 'only';
                    } else {
                        if( !$i ) {
                            $class[ ] = 'first';
                        }
                        if( $i == $childrenCount - 1 ) {
                            $class[ ] = 'last';
                        }
                    }
                    if( isset( $children[ $i + 1 ] ) && $this->isCategoryActive( $children[ $i + 1 ] ) ) {
                        $class[ ] = 'prev';
                    }
                    if( isset( $children[ $i - 1 ] ) && $this->isCategoryActive( $children[ $i - 1 ] ) ) {
                        $class[ ] = 'next';
                    }
                    $htmlChildren .= $this->drawOpenCategoryItem( $child, $level + 1, $class );
                }

                if( !empty( $htmlChildren ) ) {
                    $levelClass[ ] = 'open';

                    // indent HTML!
                    $html[ 2 ] = str_pad( "", ( $level * 2 ) + 2, " " ) . '<ul>' . "\n" . $htmlChildren . "\n" . str_pad( "", ( $level * 2 ) + 2, " " ) . '</ul>';
                }
            }
        }

        // indent HTML!
        $html[ 0 ] = str_pad( "", ( $level * 2 ) + 2, " " ) . sprintf( '<li class="%s">', implode( " ", $levelClass ) ) . "\n";

        // indent HTML!
        $html[ 3 ] = "\n" . str_pad( "", ( $level * 2 ) + 2, " " ) . '</li>' . "\n";

        ksort( $html );
        return implode( '', $html );
    }
	
	protected function _isCurrentCategory( $category )
    {
        return ( $cat = $this->getCurrentCategory() ) && $cat->getId() == $category->getId();
    }
	
	public function getStoreCategories($sorted = false, $asCollection = false, $toLoad = true)
    {
        return $this->_categoryHelper->getStoreCategories($sorted , $asCollection, $toLoad);
    }
	
	public function isCategoryActive($category)
    {
        return $this->getCurrentCategory()
            ? in_array($category->getId(), $this->getCurrentCategory()->getPathIds()) : false;
    }
	
	public function getCurrentCategory()
    {
		return $this->layerResolver->get()->getCurrentCategory();
    }
	
	protected function _getClassNameFromCategoryName( $category )
    {
        $name = $category->getName();
        $name = preg_replace( '/-{2,}/', '-', preg_replace( '/[^a-z-]/', '-', strtolower( $name ) ) );
        while( $name && $name{0} == '-' )
            $name = substr( $name, 1 );
        while( $name && substr( $name, -1 ) == '-' )
            $name = substr( $name, 0, -1 );
        return $name;
    }
	
	public function displayProductCount()
    {
		return $this->scopeConfig->getValue('catalog/vertnav/display_product_count', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

	protected function _getCategoryCollection()
    {
		$collection = $this->_categoryFactory->create()->getCollection();
        $collection->addAttributeToSelect( 'url_key' )->addAttributeToSelect( 'name' )->addAttributeToSelect( 'all_children' )->addAttributeToFilter( 'is_active', 1 )->addAttributeToFilter( 'include_in_menu', 1 )->setOrder( 'position', 'ASC' )->joinUrlRewrite();

		if( $this->displayProductCount() ) {
            $collection->setLoadProductCount( TRUE );
        }
        return $collection;
    }
    
	public function getCurrentCategoryPath()
    {
        if ($this->getCurrentCategory()) {
            return explode(',', $this->getCurrentCategory()->getPathInStore());
        }
        return array();
    }
	
	public function toLinearArray( $collection )
    {

        $array = array ();
        foreach( $collection as $item )
            $array[ ] = $item;
        return $array;
    }
	
	public function getCategoryModel($id)
    {
        $_category = $this->_categoryFactory->create();
        $_category->load($id);
        
        return $_category;
    }
}
