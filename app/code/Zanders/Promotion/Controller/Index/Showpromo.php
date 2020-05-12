<?php
/**
 * @category   Zanders
 * @package    Zanders_Promotion
 */

namespace Zanders\Promotion\Controller\Index;

use Zanders\Promotion\Model\PromotionFactory;
use Zanders\Sports\Helper\Data as SportsHelper;
use Magento\Framework\App\Action\Context;


class Showpromo extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PromotionFactory
     */
    protected $promotion;

    /**
     * @var SportsHelper
     */
    protected $sportsHelper;

    public function __construct(
        Context $context,
        PromotionFactory $promotion,
        SportsHelper $sportsHelper
    )
    {
        $this->promotion = $promotion;
        $this->sportsHelper = $sportsHelper;
        parent::__construct($context);
    }

    public function execute()
    {
        $promoId = $this->getRequest()->getParam('id');
        if ($promoId) {
            try{
                $promo = $this->promotion->create()->load($promoId);
                $fileName = str_replace(' ', '_', $promo->getTitle());
                $content = file_get_contents($this->sportsHelper->getMediaUrl() . "/promotions/" . $promo->getId() . '.pdf');
                $response = $this->getResponse();
                $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
                $response->setHeader('Content-Disposition', 'inline; filename="' . $fileName . '.pdf"', true);
                $response->setHeader('Content-Type', 'application/pdf', true);
                $response->setBody($content);
            }catch (\Exception $ex){
                $this->messageManager->addErrorMessage(__('File does not exist'));
                $this->_redirect('*/*/promotional');
            }
        }
        $this->messageManager->addErrorMessage(__('Promo does not exist'));
        $this->_redirect('*/*/promotional');
    }
}
