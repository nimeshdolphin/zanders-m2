<?php
/**
 * @category   Zanders
 * @package    Zanders_WarningCode
 */

namespace Zanders\WarningCode\ViewModel;

use Magento\Catalog\Helper\Data;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Zanders\WarningCode\Model\ResourceModel\WarningCode as ResourceWarningCode;

/**
 * Product WarningCode view model.
 */
class WarningCode extends DataObject implements ArgumentInterface
{
    /**
     * Catalog data.
     *
     * @var Data
     */
    private $catalogData;

    /**
     * @var ResourceWarningCode
     */
    private $warningCode;

    /**
     * @param Data $catalogData
     * @param ResourceWarningCode $warningCode
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        Data $catalogData,
        ResourceWarningCode $warningCode
    )
    {
        parent::__construct();
        $this->catalogData = $catalogData;
        $this->warningCode = $warningCode;
    }

    /**
     * Returns Warning Html.
     *
     * @return string|false
     */
    public function getWarningHtml()
    {
        if ($this->catalogData->getProduct()->getData('prop65warningcode')) {
            $warningCode = $this->catalogData->getProduct()->getAttributeText('prop65warningcode');
            $warningCodeData = $this->warningCode->getByCode($warningCode);

            if (empty($warningCodeData)) $warningCodeData = $this->warningCode->getByCode('SF');

            return $warningCodeData['warning_text'];
            /*return str_replace(
                ['LEAD WARNING:', 'WARNING:'],
                ['<strong>LEAD WARNING:</strong>', '<strong>WARNING:</strong>'],
                $warningCodeData['warning_text']
            );*/
        }
        return false;
    }
}
