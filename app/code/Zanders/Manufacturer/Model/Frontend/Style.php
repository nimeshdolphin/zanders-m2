<?php
/**
 * @category   Zanders
 * @package    Zanders_Manufacturer
 */

namespace Zanders\Manufacturer\Model\Frontend;


use Magento\Eav\Model\Entity\Attribute\Source\BooleanFactory;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Json as Serializer;
use Magento\Store\Model\StoreManagerInterface;

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
        $this->getAttribute()->setData(\Magento\Catalog\Model\ResourceModel\Eav\Attribute::IS_HTML_ALLOWED_ON_FRONT, 1);
        $value = parent::getValue($object);

        $manufacturerId = $object->getData($this->getAttribute()->getAttributeCode());
        $manufacturer = $this->manufacturerClassRepository->getById($manufacturerId);

        //print_r($manufacturere->getData());
        $manufacturerHtml = sprintf(
            '<a href="%s">%s</a>',
            $this->urlInterface->getUrl("catalogsearch/advanced/result") . '?manufacturer[]=' . $manufacturer->getId(),
            $manufacturer->getData('name')
        );


        //$manufacturerHtml .= "<h1>" . $manufacturer->getData('name') . "</h1>";
        /*
        class=\"data last\">
       <br>9200 Cody<br>
       Overland Park, KS 66214-1734<br>800-423-3537<br><a target=\"_blank\" href=\"http://www.bushnell.com\">www.bushnell.com</a></td>




               $info = array("<a href=\"" . Mage::getURL() . "catalogsearch/advanced/result/?manufacturer[]=" . $manufacturer->getId() . "\">" . $manufacturer->getName() . "</a>",
                   nl2br($manufacturer->getAddress(), true),
                   $manufacturer->getPhone(),
                   "<a target=\"_blank\" href=\"http://" . $manufacturer->getWeb() . "\">" . $manufacturer->getWeb() . "</a>");
               echo implode('<br />', $info);*/


        return $manufacturerHtml;
    }
}
