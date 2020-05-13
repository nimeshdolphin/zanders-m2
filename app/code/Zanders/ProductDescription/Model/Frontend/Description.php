<?php
/**
 * @category   Zanders
 * @package    Zanders_ProductDescription
 */

namespace Zanders\ProductDescription\Model\Frontend;

/**
 * Product Description source model.
 */
class Description extends \Magento\Eav\Model\Entity\Attribute\Frontend\AbstractFrontend
{
    /**
     * @var \Zanders\ProductDescription\Model\ResourceModel\ProductDescription
     */
    protected $productDescriptionResource;

    public function __construct(
        \Magento\Eav\Model\Entity\Attribute\Source\BooleanFactory $attrBooleanFactory,
        \Zanders\ProductDescription\Model\ResourceModel\ProductDescription $productDescriptionResource
    )
    {
        $this->productDescriptionResource = $productDescriptionResource;
        parent::__construct($attrBooleanFactory);
    }

    /**
     * @param \Magento\Framework\DataObject $object
     * @return mixed|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getValue(\Magento\Framework\DataObject $object)
    {
        return 'getValue';
        $value = parent::getValue($object);
        if (!$value || $value == "") return false;

        $product = $this->getAttribute()->getEntityId();

        return $this->productDescriptionResource->getDescriptionBySku($product->getSku());

        /*$manufacturerId = $object->getData($this->getAttribute()->getAttributeCode());
        $manufacturer = $this->manufacturerClassRepository->getById($manufacturerId);

        $manufacturerHtml[] = sprintf(
            '<a href="%s">%s</a>',
            $this->urlInterface->getUrl("catalogsearch/result/index") . '?manufacturer=' . $manufacturer->getId() . '&q=' . $manufacturer->getName(),
            $manufacturer->getData('name')
        );
        $manufacturerHtml[] = nl2br($manufacturer->getAddress(), true);
        $manufacturerHtml[] = $manufacturer->getPhone();
        $manufacturerHtml[] = sprintf(
            '<a target="_blank" href="%s">%s</a>',
            $this->fixUrl($manufacturer->getWeb()),
            $manufacturer->getWeb()
        );
        return implode('<br />', $manufacturerHtml);*/
    }
}
