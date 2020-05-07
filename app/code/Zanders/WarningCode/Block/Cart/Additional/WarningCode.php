<?php
/**
 * @category   Zanders
 * @package    Zanders_WarningCode
 */

namespace Zanders\WarningCode\Block\Cart\Additional;

use Zanders\WarningCode\Model\ResourceModel\WarningCode as ResourceWarningCode;

/**
 * Cart additional info WarningCode.
 */
class WarningCode extends \Magento\Framework\View\Element\Template
{
    /**
     * @var ResourceWarningCode
     */
    private $warningCode;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        ResourceWarningCode $warningCode
    ) {
        $this->warningCode = $warningCode;
        parent::__construct($context);
    }

    /**
     * Returns Warning Html.
     * @param string $warningCode
     * @return string
     */
    public function getWarningHtml($warningCode)
    {
        $warningCodeData = $this->warningCode->getByCode($warningCode);
        if (empty($warningCodeData)) $warningCodeData = $this->warningCode->getByCode('SF');
        return $warningCodeData['warning_text'];
    }
}
