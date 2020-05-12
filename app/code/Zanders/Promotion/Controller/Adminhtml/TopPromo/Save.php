<?php
/**
 * @category   Zanders
 * @package    Zanders_Promotion
 */

namespace Zanders\Promotion\Controller\Adminhtml\TopPromo;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\AdapterFactory;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Zanders\Promotion\Model\TopPromo;
use Magento\Backend\Model\Session;
use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{
    protected $topPromoModel;
    protected $adminsession;
    protected $uploaderFactory;
    protected $adapterFactory;
    protected $directoryList;
    protected $filesystem;

    public function __construct(
        Action\Context $context,
        TopPromo $topPromoModel,
        Session $adminsession,
        DirectoryList $directoryList,
        UploaderFactory $uploaderFactory,
        AdapterFactory $adapterFactory,
        Filesystem $filesystem
    )
    {
        parent::__construct($context);
        $this->topPromoModel = $topPromoModel;
        $this->adminsession = $adminsession;
        $this->directoryList = $directoryList;
        $this->uploaderFactory = $uploaderFactory;
        $this->adapterFactory = $adapterFactory;
        $this->filesystem = $filesystem;
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            try {
                if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != '') {
                    try {
                        $uploaderFactory = $this->uploaderFactory->create(['fileId' => 'image']);
                        $uploaderFactory->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                        $imageAdapter = $this->adapterFactory->create();
                        $uploaderFactory->addValidateCallback('custom_image_upload', $imageAdapter, 'validateUploadFile');
                        $uploaderFactory->setAllowRenameFiles(true);
                        $uploaderFactory->setFilesDispersion(true);
                        $mediaDirectory = $this->filesystem->getDirectoryRead($this->directoryList::MEDIA);
                        $destinationPath = $mediaDirectory->getAbsolutePath('promotions/covers');
                        $result = $uploaderFactory->save($destinationPath);
                        if (!$result) {
                            throw new LocalizedException(
                                __('File cannot be saved to path: $1', $destinationPath)
                            );
                        }
                        $imagePath = 'promotions/covers' . $result['file'];
                        $data['image'] = $imagePath;
                    } catch (\Exception $e) {
                    }
                }
                if (isset($data['image']['delete']) && $data['image']['delete'] == 1) {
                    $mediaDirectory = $this->filesystem->getDirectoryRead($this->directoryList::MEDIA)->getAbsolutePath();
                    $file = $data['image']['value'];
                    $imgPath = $mediaDirectory . $file;
                    if ($this->_file->isExists($imgPath)) {
                        $this->_file->deleteFile($imgPath);
                    }
                    $data['image'] = NULL;
                }
                if (isset($data['image']['value'])) {
                    $data['image'] = $data['image']['value'];
                }
                $toppromo_id = $this->getRequest()->getParam('toppromo_id');
                if ($toppromo_id) {
                    $this->topPromoModel->load($toppromo_id);
                }
                $this->topPromoModel->setData($data);
                $this->topPromoModel->save();

                $this->messageManager->addSuccessMessage(__('The data has been saved.'));
                $this->adminsession->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    if ($this->getRequest()->getParam('back') == 'add') {
                        return $resultRedirect->setPath('*/*/add');
                    } else {
                        return $resultRedirect->setPath(
                            '*/*/edit',
                            [
                                'toppromo_id' => $this->topPromoModel->getToppromoId(),
                                '_current' => true
                            ]
                        );
                    }
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the data.'));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['toppromo_id' => $this->getRequest()->getParam('toppromo_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zanders_Promotion::top_promo');
    }
}
