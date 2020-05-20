<?php
/**
 * @category   Zanders
 * @package    Zanders_Promotion
 */

namespace Zanders\Promotion\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\AdapterFactory;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Zanders\Promotion\Model\Promotion;
use Magento\Backend\Model\Session;
use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{
    protected $promotionModel;
    protected $adminsession;
    protected $uploaderFactory;
    protected $adapterFactory;
    protected $directoryList;
    protected $filesystem;

    public function __construct(
        Action\Context $context,
        Promotion $promotionModel,
        Session $adminsession,
        DirectoryList $directoryList,
        UploaderFactory $uploaderFactory,
        AdapterFactory $adapterFactory,
        Filesystem $filesystem
    )
    {
        parent::__construct($context);
        $this->promotionModel = $promotionModel;
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
            $id = $this->getRequest()->getParam('id');

            if ($id) {
                $this->promotionModel->load($id);
            }

            $this->promotionModel->setData($data);

            try {
                $promotion = $this->promotionModel->save();
                if (isset($_FILES['pdf']['name']) && $_FILES['pdf']['name'] != '') {
                    try {
                        $uploaderFactory = $this->uploaderFactory->create(['fileId' => 'pdf']);
                        $uploaderFactory->setAllowedExtensions(['pdf']);
                        $uploaderFactory->setAllowRenameFiles(false);
                        $mediaDirectory = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
                        $destinationPath = $mediaDirectory->getAbsolutePath('promotions');
                        $newFileName = $promotion->getId() . "." . $uploaderFactory->getFileExtension();
                        $result = $uploaderFactory->save($destinationPath, $newFileName);
                        if (!$result) {
                            throw new LocalizedException(
                                __('File cannot be saved to path: $1', $destinationPath)
                            );
                        }
                        // $imagePath = 'promotions' . $result['file'];
                    } catch (\Exception $e) {
                        throw new LocalizedException(
                            __('Error uploading file, please try again.')
                        );
                    }
                }

                $this->messageManager->addSuccessMessage(__('The data has been saved.'));
                $this->adminsession->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    if ($this->getRequest()->getParam('back') == 'add') {
                        return $resultRedirect->setPath('*/*/add');
                    } else {
                        return $resultRedirect->setPath(
                            '*/*/edit',
                            [
                                'id' => $this->promotionModel->getId(),
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
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zanders_Promotion::promotion');
    }
}
