<?php
/**
 * @category   Zanders
 * @package    Zanders_Sports
 */

namespace Zanders\Sports\Controller\Index;

use Magento\Framework\Controller\ResultFactory;

class FflFile extends \Magento\Framework\App\Action\Action
{
    protected $customerSession;
    protected $customerFactory;
    protected $urlInterface;
    protected $formKey;
    protected $sportsHelper;
    protected $elliottHelper;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Zanders\Sports\Helper\Data $sportsHelper,
        \Zanders\Elliott\Helper\Data $elliottHelper,
        array $data = []
    )
    {
        $this->customerSession = $customerSession;
        $this->customerFactory = $customerFactory;
        $this->urlInterface = $urlInterface;
        $this->formKey = $formKey;
        $this->sportsHelper = $sportsHelper;
        $this->elliottHelper = $elliottHelper;
        parent::__construct($context);
    }

    public function execute()
    {
        if ($this->customerSession->isLoggedIn()) {

            $fflnum = $this->getRequest()->getParam('fflnumber');
            $html = '<html>' . $this->elliottHelper->validateFFLUploadScript() . '
            <body><form id="uploadfflfile" action="' . $this->urlInterface->getUrl('zanders/index/fflupload', ['fflnumber' => $fflnum]) . '" method="post" enctype="multipart/form-data"><input type="hidden" name="form_key" value="' . $this->formKey->getFormKey() . '"><input id="fflfile" name="fflfile" value="" class="required-entry" type="file"><input type="button" value="Upload File" onclick="Filevalidation()"></form>
            </body></html>';

            $this->getResponse()->setBody($html);
        }
    }
}
