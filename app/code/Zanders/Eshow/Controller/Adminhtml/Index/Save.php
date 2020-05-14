<?php
/**
 * @category   Zanders
 * @package    Zanders_Eshow
 */

namespace Zanders\Eshow\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\AdapterFactory;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Backend\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Zanders\Eshow\Model\Eshow;
use Zanders\Eshow\Model\EshowManufacturer;
use Zanders\Eshow\Model\ResourceModel\EshowManufacturer as EshowManufacturerResource;

class Save extends \Magento\Backend\App\Action
{
    protected $eshowModel;
    protected $eshowManufacturer;
    protected $eshowManufacturerResource;
    protected $adminsession;
    protected $uploaderFactory;
    protected $adapterFactory;
    protected $directoryList;
    protected $filesystem;

    public function __construct(
        Action\Context $context,
        Eshow $eshowModel,
        EshowManufacturer $eshowManufacturer,
        EshowManufacturerResource $eshowManufacturerResource,
        Session $adminsession,
        DirectoryList $directoryList,
        UploaderFactory $uploaderFactory,
        AdapterFactory $adapterFactory,
        Filesystem $filesystem
    )
    {
        parent::__construct($context);
        $this->eshowModel = $eshowModel;
        $this->eshowManufacturer = $eshowManufacturer;
        $this->eshowManufacturerResource = $eshowManufacturerResource;
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
            $eshowId = $this->getRequest()->getParam('id');

            if ($eshowId) {
                $this->eshowModel->load($eshowId);
            }
            $this->eshowModel->setData($data);

            try {
                $eshow = $this->eshowModel->save();

                // Uploading PDF file
                if (isset($_FILES['eshowpdf']['name']) && $_FILES['eshowpdf']['name'] != '') {
                    try {
                        $uploaderFactory = $this->uploaderFactory->create(['fileId' => 'eshowpdf']);
                        $uploaderFactory->setAllowedExtensions(['pdf']);
                        $uploaderFactory->setAllowRenameFiles(false);
                        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
                        $destinationPath = $mediaDirectory->getAbsolutePath('eshows');
                        $newFileName = $eshow->getId() . "." . $uploaderFactory->getFileExtension();
                        $result = $uploaderFactory->save($destinationPath, $newFileName);
                        if (!$result) {
                            throw new LocalizedException(
                                __('File cannot be saved to path: $1', $destinationPath)
                            );
                        }
                        // $imagePath = 'eshows' . $result['file'];
                    } catch (\Exception $e) {
                        throw new LocalizedException(
                            __('Error uploading file, please try again.')
                        );
                    }
                }

                // Saving Manufacturers
                $existingManufacturers = $this->eshowManufacturerResource->getByShowId($eshow->getId());
                $manufacturersToAdd = $data['manufacturers'];
                foreach ($manufacturersToAdd as $manufacturerToAdd) {
                    if (in_array($manufacturerToAdd, $existingManufacturers)) continue;
                    $this->eshowManufacturer->unsetData();
                    $eshowManufacturer = ['show_id' => $this->eshowModel->getId(), 'manufacturer_id' => $manufacturerToAdd];
                    $this->eshowManufacturer->setData($eshowManufacturer);
                    $this->eshowManufacturer->save();
                }

                $manufacturersToDelete = (array_diff($existingManufacturers, $manufacturersToAdd));
                if (!empty($manufacturersToDelete))
                    echo $this->eshowManufacturerResource->deleteByManufacturerIds($this->eshowModel->getId(),$manufacturersToDelete);


                $this->messageManager->addSuccessMessage(__('The data has been saved.'));
                $this->adminsession->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    if ($this->getRequest()->getParam('back') == 'add') {
                        return $resultRedirect->setPath('*/*/add');
                    } else {
                        return $resultRedirect->setPath(
                            '*/*/edit',
                            [
                                'id' => $this->eshowModel->getId(),
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
                var_dump($e->getMessage());
                exit;
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the data.'));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zanders_Eshow::eshow');
    }
}
