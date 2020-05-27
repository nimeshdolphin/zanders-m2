<?php
/**
 * @category   Zanders
 * @package    Zanders_Sports
 */

namespace Zanders\Sports\Controller\Index;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Zend_Mail;
use Zend_Mime;

class FflUpload extends \Magento\Framework\App\Action\Action
{
    protected $customerSession;
    protected $customerFactory;
    protected $urlInterface;
    protected $uploaderFactory;
    protected $fileSystem;
    protected $formKey;
    protected $sportsHelper;
    protected $sportsConfigHelper;
    protected $elliottHelper;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\UrlInterface $urlInterface,
        UploaderFactory $uploaderFactory,
        Filesystem $fileSystem,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Zanders\Sports\Helper\Data $sportsHelper,
        \Zanders\Sports\Helper\Config $sportsConfigHelper,
        \Zanders\Elliott\Helper\Data $elliottHelper,
        array $data = []
    )
    {
        $this->customerSession = $customerSession;
        $this->customerFactory = $customerFactory;
        $this->urlInterface = $urlInterface;
        $this->uploaderFactory = $uploaderFactory;
        $this->fileSystem = $fileSystem;
        $this->formKey = $formKey;
        $this->sportsHelper = $sportsHelper;
        $this->sportsConfigHelper = $sportsConfigHelper;
        $this->elliottHelper = $elliottHelper;
        parent::__construct($context);
    }

    public function execute()
    {
        if ($this->customerSession->isLoggedIn()) {
            $fflnum = $this->getRequest()->getParam('fflnumber');

            $customer = $this->customerFactory->create();
            $customer->load($this->customerSession->getCustomer()->getId());

            if (file_exists($_FILES['fflfile']['tmp_name'])) {
                $type = $_FILES['fflfile']['type'];
                $allowed_types = array('application/pdf', 'image/png', 'image/x-png', 'image/pjpeg', 'image/jpg', 'image/jpeg', 'image/gif', 'image/tiff');
                if (in_array($type, $allowed_types)) {
                    if ($_FILES['fflfile']['size'] > 10485760) {
                        $html = '<html>' . $this->elliottHelper->validateFFLUploadScript() . '
                        <body>Max Allowed File Size 10MB!<form id="uploadfflfile" action="' . $this->urlInterface->getUrl('zanders/index/fflupload', ['fflnumber' => $fflnum]) . '" method="post" enctype="multipart/form-data"><input type="hidden" name="form_key" value="' . $this->formKey->getFormKey() . '"><input id="fflfile" name="fflfile" value="" class="required-entry" type="file"><input type="button" value="Upload File" onclick="Filevalidation()"></form>
                        </body></html>';
                        $this->getResponse()->setBody($html);
                    } else {
                        $fileType = pathinfo($_FILES['fflfile']['name'], PATHINFO_EXTENSION);
                        $mail = new Zend_Mail();
                        $attachment = $mail->createAttachment(fopen($_FILES['fflfile']['tmp_name'], 'r'));
                        $attachment->type = $_FILES['fflfile']['type'];
                        $attachment->disposition = Zend_Mime::DISPOSITION_ATTACHMENT;
                        $attachment->encoding = Zend_Mime::ENCODING_BASE64;
                        $attachment->filename = 'ffl_' . $fflnum . '.' . strtolower($fileType);

                        $mail->setBodyHtml('Attached Should be a copy of FFL from ' . $customer->getName() . ', FFL Number: ' . $fflnum);
                        $mail->setFrom($customer->getEmail(), $customer->getName());
                        $mail->addTo($this->sportsConfigHelper->getConfig('sports/zandersdefaults/ffldropemail'), 'FFL Update');
                        $mail->setSubject("FFL Update from " . $customer->getName() . " for account number" . $customer->getNumber() . ", FFL Number:" . $fflnum);
                        $mail->send();

                        $uploaderFactory = $this->uploaderFactory->create(['fileId' => 'fflfile']);
                        $uploaderFactory->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png', 'pdf']);
                        $uploaderFactory->setAllowRenameFiles(false);
                        $uploaderFactory->setFilesDispersion(false);
                        $mediaDirectory = $this->fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
                        $destinationPath = $mediaDirectory->getAbsolutePath('ffls/' . $customer->getNumber());
                        $result = $uploaderFactory->save($destinationPath, $fflnum . '.' . strtolower($fileType));
                        if (!$result) {
                            throw new LocalizedException(__('File cannot be saved'));
                        }

                        $html = '<html><body>FFL File Uploaded!</body></html>';
                        $this->getResponse()->setBody($html);
                    }
                } else {
                    $html = '<html>' . $this->elliottHelper->validateFFLUploadScript() . '
                    <body>The file that was uploaded was not in the correct format.<form id="uploadfflfile" action="' . $this->urlInterface->getUrl('zanders/index/fflupload', ['fflnumber' => $fflnum]) . '" method="post" enctype="multipart/form-data"><input type="hidden" name="form_key" value="' . $this->formKey->getFormKey() . '"><input id="fflfile" name="fflfile" value="" class="required-entry" type="file"><input type="button" value="Upload File" onclick="Filevalidation()"></form>
                    </body></html>';
                    $this->getResponse()->setBody($html);
                }
            } else {
                $html = '<html>' . $this->elliottHelper->validateFFLUploadScript() . '
                <body><form id="uploadfflfile" action="' . $this->urlInterface->getUrl('zanders/index/fflupload', ['fflnumber' => $fflnum]) . '" method="post" enctype="multipart/form-data"><input type="hidden" name="form_key" value="' . $this->formKey->getFormKey() . '"><input id="fflfile" name="fflfile" value="" class="required-entry" type="file"><input type="button" value="Upload File" onclick="Filevalidation()"></form>
                </body></html>';
                $this->getResponse()->setBody($html);
            }
        }
    }
}
