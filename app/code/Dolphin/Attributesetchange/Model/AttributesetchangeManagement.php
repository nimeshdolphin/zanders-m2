<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Dolphin\Attributesetchange\Model;

use Magento\Catalog\Controller\Adminhtml\Product\Builder;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\Product\Type\AbstractType;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Eav\Model\Entity\Attribute\SetFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Eav\Model\EntityFactory;
use Magento\Framework\Exception\CouldNotSaveException;

class AttributesetchangeManagement implements \Dolphin\Attributesetchange\Api\AttributesetchangeManagementInterface
{

	protected $_productRepository;	
	/**
     * Massactions filter
     */     
    protected $filter;
    
    /**
     * ScopeConfigInterface scopeConfig
     */
    protected $scopeConfig;
  
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
   
    /** 
     * @var \Magento\Eav\Model\Entity\Attribute\SetFactory
     */
    protected $attributeSetFactory;

	/**
	 *  @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable 
	 */
    protected $configurableProduct;

    /** 
     * @var \Magento\Eav\Model\EntityFactory 
     */
    protected $entityFactory;
    
    /**
     * @param Builder $productBuilder
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param SetFactory $attributeSetFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param Configurable $configurableProduct
     * @param EntityFactory $entityFactory
     */
    public function __construct(        
        Filter $filter,
        CollectionFactory $collectionFactory,
        SetFactory $attributeSetFactory,
        ScopeConfigInterface $scopeConfig,
        Configurable $configurableProduct,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        EntityFactory $entityFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $attributeCollaction
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->scopeConfig = $scopeConfig;
        $this->configurableProduct = $configurableProduct;
        $this->_productRepository = $productRepository;
        $this->entityFactory = $entityFactory;        
        $this->attributeCollaction = $attributeCollaction;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributesetchange($productIds,$attribute)
    {        
        $storeId = 0 ;	
		$attribute_set_collection = $this->attributeCollaction->create();
		$attribute_set_collection
			->addFieldToFilter('entity_type_id',4)
			->addFieldToFilter('attribute_set_name',$attribute);

		$att_set = current($attribute_set_collection->getData());
		$attribute_set_id = $att_set["attribute_set_id"];

        $attributeSetId = $attribute_set_id;         
        $productIds = explode(',', $productIds);

        try {       
             foreach ($productIds as $id) {
             	$product = $this->_productRepository->getById($id);
             	if(!$product->getId())
             	{
             		throw new CouldNotSaveException(__('ProductId Not Valid'));	
             	}

				if($this->validateConfigurable($product,$attributeSetId,$storeId) == false)
				{
					throw new CouldNotSaveException(__('Validation Failed'));
                }

				$product->setAttributeSetId($attributeSetId)->setStoreId($storeId);               
				$product->save();
             }
                     
            
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
        	
        	throw new CouldNotSaveException(__(
				$exception->getMessage()
			));	

        } 

        $data =array ('id'=>$productIds,'attribute_set'=>$attribute);
        return array('product'=>$data);    
    }



    private function validateConfigurable($product,$attributeSetId,$storeId)
    { 
		$type = $product->getTypeInstance();
		if(!$type instanceof Configurable)
		{
			return true;
		}
        $attributeSet = $this->attributeSetFactory->create()->load($attributeSetId);
        $attributes = $this->configurableProduct->getUsedProductAttributes($product);
        $attributeSet->addSetInfo(
            $this->entityFactory->create()->setType(\Magento\Catalog\Model\Product::ENTITY)->getTypeId(),
            $attributes
        );
        foreach ($type->getConfigurableAttributes($product) as $configAattribute) {
            $attribute  = $configAattribute->getProductAttribute();
            if (!is_null($attribute)) {
				if (!$attribute->isInSet($attributeSetId)) {
					$attribute->setAttributeSetId(
						$attributeSetId
					)->setAttributeGroupId(
						$attributeSet->getDefaultGroupId($attributeSetId)
					)->save();
					return true;
			}
				else
				{
					return true;
				}
            }
        }
    } 
}

