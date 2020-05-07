<?php

namespace Zanders\Manufacturer\Plugin\Catalog\Helper;

use Magento\Catalog\Model\Product as ModelProduct;
use Magento\Eav\Model\Config;
use Magento\Framework\Escaper;

class OutputPlugin
{


    /**
     * Eav config
     *
     * @var Config
     */
    protected $_eavConfig;

    /**
     * @var Escaper
     */
    protected $_escaper;

    /**
     * @var array
     */
    private $directivePatterns;

    /**
     * Output constructor.
     * @param Context $context
     * @param Config $eavConfig
     * @param Data $catalogData
     * @param Escaper $escaper
     * @param array $directivePatterns
     * @param array $handlers
     */
    public function __construct(
        Config $eavConfig,
        Escaper $escaper
    ) {
        $this->_eavConfig = $eavConfig;
        $this->_escaper = $escaper;
    }
    public function beforeProductAttribute(\Magento\Catalog\Helper\Output $subject, $product, $attributeHtml, $attributeName)
    {
        if($attributeName == 'manufacturer'){
            $attribute = $this->_eavConfig->getAttribute(ModelProduct::ENTITY, $attributeName);
            $attribute->setData(\Magento\Catalog\Model\ResourceModel\Eav\Attribute::IS_HTML_ALLOWED_ON_FRONT, 1);
            return [$product, $this->_escaper->escapeHtml('<b>' . $attributeHtml . '</b>'), $attributeName];
        }else{
            return [$product, $attributeHtml, $attributeName];
        }
    }
}