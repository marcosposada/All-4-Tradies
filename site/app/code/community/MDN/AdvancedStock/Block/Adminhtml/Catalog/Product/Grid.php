<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2009 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @author : Olivier ZIMMERMANN
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_AdvancedStock_Block_Adminhtml_Catalog_Product_Grid extends Mage_Adminhtml_Block_Catalog_Product_Grid
{

    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $manufacturer_items = Mage::getModel('eav/entity_attribute_option')->getCollection()->setStoreFilter()->join('attribute','attribute.attribute_id=main_table.attribute_id', 'attribute_code');
        foreach ($manufacturer_items as $manufacturer_item) :
            if ($manufacturer_item->getAttributeCode() == 'manufacturer')
                $manufacturer_options[$manufacturer_item->getOptionId()] = $manufacturer_item->getValue();
        endforeach;

        $this->addColumn('manufacturer',
            array(
                'header'=> Mage::helper('catalog')->__('Manufacturer'),
                'width' => '100px',
                'type'  => 'options',
                'index' => 'manufacturer',
                'options' => $manufacturer_options
            ));

        //replace qty column
        $this->addColumn('qty', array(
            'header'=> Mage::helper('AdvancedStock')->__('Stock Summary'),
            'index' => 'entity_id',
            'renderer'	=> 'MDN_AdvancedStock_Block_Product_Widget_Grid_Column_Renderer_StockSummary',
            'filter' => 'MDN_AdvancedStock_Block_Product_Widget_Grid_Column_Filter_StockSummary',
            'sortable'	=> false
        ));

    }

}
