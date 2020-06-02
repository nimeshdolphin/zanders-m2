<?php

declare(strict_types=1);

namespace Zanders\Sports\Observer\Catalog;

class ProductFlatPrepareColumns implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {

        $columns = $observer->getColumns()->getColumns();
        $dontchange = array('name','links_title','image_label','small_image','small_image_label','thumbnail','thumbnail_label','url_key','url_path','other_features','other_features2','sku');

        foreach($columns as $type=>$column){
            if(!in_array($type,$dontchange) && $column['type']=='varchar(255)'){                
                $columns[$type]['type'] = 'varchar(64)';
            }
        }
        $observer->getColumns()->setColumns($columns);
    }
}

