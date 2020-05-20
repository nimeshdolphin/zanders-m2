<?php
/**
 * @category   Zanders
 * @package    Zanders_Manufacturer
 */

namespace Zanders\Manufacturer\Model\Frontend;

/**
 * Product manufacturer source model.
 */
class Style extends \Magento\Eav\Model\Entity\Attribute\Frontend\AbstractFrontend
{
    /**
     * @var \Zanders\Manufacturer\Api\ManufacturerRepositoryInterface
     */
    protected $manufacturerClassRepository;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlInterface;

    public function __construct(
        \Magento\Eav\Model\Entity\Attribute\Source\BooleanFactory $attrBooleanFactory,
        \Magento\Framework\UrlInterface $urlInterface,
        \Zanders\Manufacturer\Api\ManufacturerRepositoryInterface $manufacturerClassRepository
    )
    {
        $this->manufacturerClassRepository = $manufacturerClassRepository;
        $this->urlInterface = $urlInterface;
        parent::__construct($attrBooleanFactory);
    }

    /**
     * @param \Magento\Framework\DataObject $object
     * @return mixed|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getValue(\Magento\Framework\DataObject $object)
    {
        $value = parent::getValue($object);
        if (!$value || $value == "") return false;

        $this->getAttribute()->setData(\Magento\Catalog\Model\ResourceModel\Eav\Attribute::IS_HTML_ALLOWED_ON_FRONT, 1);
        $manufacturerId = $object->getData($this->getAttribute()->getAttributeCode());
        $manufacturer = $this->manufacturerClassRepository->getById($manufacturerId);

        $manufacturerHtml[] = sprintf(
            '<a href="%s">%s</a>',
            $this->urlInterface->getUrl("catalogsearch/result/index") . '?manufacturer=' . $manufacturer->getId() . '&q=' . $manufacturer->getName(),
            $manufacturer->getData('name')
        );
        if ($manufacturer->getAddress())
            $manufacturerHtml[] = nl2br($manufacturer->getAddress(), true);
        if ($manufacturer->getPhone())
            $manufacturerHtml[] = $manufacturer->getPhone();
        if ($manufacturer->getWeb())
            $manufacturerHtml[] = sprintf(
                '<a target="_blank" href="%s">%s</a>',
                $this->fixUrl($manufacturer->getWeb()),
                $manufacturer->getWeb()
            );
        return implode('<br />', $manufacturerHtml);
    }

    private function fixUrl($url)
    {
        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
            $url = "http://" . $url;
        }
        return $url;
    }
}
