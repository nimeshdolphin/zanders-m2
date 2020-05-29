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
use Magento\Catalog\Model\ProductFactory;
use Magento\Backend\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Zanders\Eshow\Model\Eshow;
use Zanders\Eshow\Model\EshowManufacturer;
use Zanders\Eshow\Model\ResourceModel\EshowManufacturer as EshowManufacturerResource;
use Zanders\Eshow\Model\EshowPurchasedItem;
use Zanders\Eshow\Model\ResourceModel\EshowPurchasedItem as EshowPurchasedItemResource;
use Zanders\Eshow\Model\EshowReceivedItem;
use Zanders\Eshow\Model\ResourceModel\EshowReceivedItem as EshowReceivedItemResource;

class Save extends \Magento\Backend\App\Action
{
    protected $eshowModel;
    protected $eshowManufacturer;
    protected $eshowManufacturerResource;
    protected $eshowPurchasedItem;
    protected $eshowPurchasedItemResource;
    protected $eshowReceivedItem;
    protected $eshowReceivedItemResource;
    protected $adminsession;
    protected $uploaderFactory;
    protected $adapterFactory;
    protected $directoryList;
    protected $filesystem;
    protected $productFactory;

    public function __construct(
        Action\Context $context,
        Session $adminsession,
        DirectoryList $directoryList,
        UploaderFactory $uploaderFactory,
        AdapterFactory $adapterFactory,
        Filesystem $filesystem,
        ProductFactory $productFactory,
        Eshow $eshowModel,
        EshowManufacturer $eshowManufacturer,
        EshowManufacturerResource $eshowManufacturerResource,
        EshowPurchasedItem $eshowPurchasedItem,
        EshowPurchasedItemResource $eshowPurchasedItemResource,
        EshowReceivedItem $eshowReceivedItem,
        EshowReceivedItemResource $eshowReceivedItemResource
    ) {
        parent::__construct($context);
        $this->adminsession = $adminsession;
        $this->directoryList = $directoryList;
        $this->uploaderFactory = $uploaderFactory;
        $this->adapterFactory = $adapterFactory;
        $this->filesystem = $filesystem;
        $this->productFactory = $productFactory;
        $this->eshowModel = $eshowModel;
        $this->eshowManufacturer = $eshowManufacturer;
        $this->eshowManufacturerResource = $eshowManufacturerResource;
        $this->eshowPurchasedItem = $eshowPurchasedItem;
        $this->eshowPurchasedItemResource = $eshowPurchasedItemResource;
        $this->eshowReceivedItem = $eshowReceivedItem;
        $this->eshowReceivedItemResource = $eshowReceivedItemResource;
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
                    if (in_array($manufacturerToAdd, $existingManufacturers)) {
                        continue;
                    }
                    $this->eshowManufacturer->unsetData();
                    $eshowManufacturer = ['show_id' => $this->eshowModel->getId(), 'manufacturer_id' => $manufacturerToAdd];
                    $this->eshowManufacturer->setData($eshowManufacturer);
                    $this->eshowManufacturer->save();
                }

                $manufacturersToDelete = (array_diff($existingManufacturers, $manufacturersToAdd));
                if (!empty($manufacturersToDelete)) {
                    $this->eshowManufacturerResource->deleteByManufacturerIds($this->eshowModel->getId(), $manufacturersToDelete);
                }

                // Saving Purchase Items
                if (isset($data['pursku'])) {
                    $existingPurchaseItems = $this->eshowPurchasedItemResource->getIdSkuPairs($eshow->getId());
                    foreach ($data['pursku'] as $sku) {
                        // Delete
                        if (isset($data['purremove'][$sku]) && $data['purremove'][$sku] == '1') {
                            $this->eshowPurchasedItemResource->deleteBySkus($this->eshowModel->getId(), [$sku]);
                            continue;
                        }

                        $this->eshowPurchasedItem->unsetData();
                        if (in_array($sku, $existingPurchaseItems)) {
                            $this->eshowPurchasedItem->load(array_search($sku, $existingPurchaseItems));
                        }

                        $product = $this->productFactory->create();
                        $product->load($product->getIdBySku($sku));

                        $this->eshowPurchasedItem->setData('show_id', $this->eshowModel->getId());
                        $this->eshowPurchasedItem->setData('item_id', $product->getId());
                        $this->eshowPurchasedItem->setData('sku', $sku);
                        $this->eshowPurchasedItem->setData('description', (trim($product->getName()) == trim($data['purdesc'][$sku])) ? null : $data['purdesc'][$sku]);
                        $this->eshowPurchasedItem->setData('min_qty', ((int)$data['purminqty'][$sku] <= 0) ? null : $data['purminqty'][$sku]);
                        $this->eshowPurchasedItem->setData('max_qty', ((int)$data['purmaxqty'][$sku] <= 0) ? null : $data['purmaxqty'][$sku]);
                        $this->eshowPurchasedItem->setData('custom_price', ($data['purprice'][$sku] <= 0 || $product->getPrice() == $data['purprice'][$sku]) ? null : $data['purprice'][$sku]);

                        $this->eshowPurchasedItem->save();
                    }
                }

                if (isset($data['recsku'])) {
                    // Saving Receive Items
                    $existingReceiveItems = $this->eshowReceivedItemResource->getIdSkuPairs($eshow->getId());
                    foreach ($data['recsku'] as $sku) {
                        // Delete
                        if (isset($data['recremove'][$sku]) && $data['recremove'][$sku] == '1') {
                            $this->eshowReceivedItemResource->deleteBySkus($this->eshowModel->getId(), [$sku]);
                            continue;
                        }

                        $this->eshowReceivedItem->unsetData();
                        if (in_array($sku, $existingReceiveItems)) {
                            $this->eshowReceivedItem->load(array_search($sku, $existingReceiveItems));
                        }

                        $product = $this->productFactory->create();
                        $product->load($product->getIdBySku($sku));

                        $this->eshowReceivedItem->setData('show_id', $this->eshowModel->getId());
                        $this->eshowReceivedItem->setData('item_id', $product->getId());
                        $this->eshowReceivedItem->setData('sku', $sku);
                        $this->eshowReceivedItem->setData('description', (trim($product->getName()) == trim($data['recdesc'][$sku])) ? null : $data['recdesc'][$sku]);
                        $this->eshowReceivedItem->setData('min_qty', ((int)$data['recminqty'][$sku] <= 0) ? null : $data['recminqty'][$sku]);
                        $this->eshowReceivedItem->setData('max_qty', ((int)$data['recmaxqty'][$sku] <= 0) ? null : $data['recmaxqty'][$sku]);
                        $this->eshowReceivedItem->setData('custom_price', ($data['recprice'][$sku] <= 0 || $product->getPrice() == $data['recprice'][$sku]) ? null : $data['recprice'][$sku]);

                        $this->eshowReceivedItem->save();
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
