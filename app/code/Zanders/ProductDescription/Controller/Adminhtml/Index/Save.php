<?php
/**
 * @category   Zanders
 * @package    Zanders_ProductDescription
 */

namespace Zanders\ProductDescription\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filter\FilterManager;
use Zanders\ProductDescription\Model\ProductDescription;
use Magento\Backend\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\Directory\WriteInterface;


class Save extends \Magento\Backend\App\Action
{
    protected $productDescriptionModel;
    protected $adminsession;
    protected $directoryList;
    protected $filesystem;
    protected $filterManager;
    /**
     * @var ProductFactory
     */
    protected $productFactory;

    public function __construct(
        Action\Context $context,
        ProductDescription $productDescriptionModel,
        Session $adminsession,
        DirectoryList $directoryList,
        Filesystem $filesystem,
        FilterManager $filterManager,
        ProductFactory $productFactory
    )
    {
        parent::__construct($context);
        $this->productDescriptionModel = $productDescriptionModel;
        $this->adminsession = $adminsession;
        $this->directoryList = $directoryList;
        $this->filesystem = $filesystem;
        $this->filterManager = $filterManager;
        $this->productFactory = $productFactory;
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data) {
            $description_id = $this->getRequest()->getParam('description_id');
            $html = $data['html'];
            $text = preg_replace("/(?<=[^\r]|^)\n/", "\r\n", preg_replace("/[\r\n]+/", "\n", trim($this->filterManager->removeTags($html))));
            $skuList = explode("\n", str_replace(array("\r\n", "\r"), "\n", $data['sku']));

            foreach($skuList as $sku) {
                $sku = trim($sku);
                $product_id = $this->productFactory->create()->getIdBySku($sku);

                $mediaDirectory = $this->filesystem->getDirectoryWrite(
                    DirectoryList::MEDIA
                );
                $mediaPath = $this->directoryList->getPath('media');
                $fileName = $this->getFileName($sku);
                $htmlFilePath = $mediaPath . '/descriptions/html/' . $fileName;
                $textFilePath = $mediaPath . '/descriptions/text/' . $fileName;

                $this->write($mediaDirectory, $htmlFilePath, $html);
                $this->write($mediaDirectory, $textFilePath, $text);

                $productDescription = $this->productDescriptionModel->unsetData();
                if ($description_id) {
                    $productDescription->load($description_id);
                }
                $productDescription
                    ->setData('product_id', $product_id)
                    ->setData('sku', $sku)
                    ->setData('updated', time())
                    ->setData('html', $html)
                    ->setData('text', $text)
                    ->setData('file_loc', $fileName);

                $productDescription->save();
            }

            try {
                $this->messageManager->addSuccessMessage(__('The data has been saved.'));
                $this->adminsession->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    if ($this->getRequest()->getParam('back') == 'add') {
                        return $resultRedirect->setPath('*/*/add');
                    } else {
                        return $resultRedirect->setPath(
                            '*/*/edit',
                            [
                                'description_id' => $this->productDescriptionModel->getDescriptionId(),
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
            return $resultRedirect->setPath('*/*/edit', ['description_id' => $this->getRequest()->getParam('description_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Write content to text file
     *
     * @param WriteInterface $writeDirectory
     * @param $filePath
     * @param $fileData
     * @return bool
     * @throws FileSystemException
     */
    public function write(WriteInterface $writeDirectory, string $filePath, $fileData)
    {
        $stream = $writeDirectory->openFile($filePath, 'w+');
        $stream->lock();
        $stream->write($fileData);
        $stream->unlock();
        $stream->close();
        return true;
    }

    public function getFileName($sku)
    {
        $folder1 = substr($sku, 0, 1);
        $folder2 = substr($sku, 1, 1);
        return $folder1 . '/' . $folder2 . '/' . $sku . '.txt';
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zanders_ProductDescription::description');
    }
}
