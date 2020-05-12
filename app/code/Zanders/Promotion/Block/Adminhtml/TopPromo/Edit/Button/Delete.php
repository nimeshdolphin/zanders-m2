<?php
/**
 * @category   Zanders
 * @package    Zanders_Promotion
 */

namespace Zanders\Promotion\Block\Adminhtml\TopPromo\Edit\Button;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Delete extends Generic implements ButtonProviderInterface
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @param Context $context
     * @param AuthorRepositoryInterface $authorRepository
     */

    public function __construct(
        Context $context
    ) {
        $this->context = $context;
    }

    /**
     * get button data
     *
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        $toppromo_id = $this->context->getRequest()->getParam('toppromo_id');
        if ($toppromo_id) {
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to do this?'
                ) . '\', \'' . $this->getDeleteUrl() . '\')',
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        $toppromo_id = $this->context->getRequest()->getParam('toppromo_id');
        return $this->getUrl('*/*/delete', ['toppromo_id' => $toppromo_id]);
    }
}
