<?php

    $installer = $this;
    $installer->startSetup();

    $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
    $setup->addAttribute('customer', 'trade_other', array(
        'type' => 'varchar',
        'input' => 'text',
        'label' => 'Other Trade',
        'global' => 1,
        'visible' => 1,
        'required' => 0,
        'user_defined' => 1,
        'default' => NULL,
        'visible_on_front' => 0,
    ));
    if (version_compare(Mage::getVersion(), '1.4.2', '>=')) {
        Mage::getSingleton('eav/config')
            ->getAttribute('customer', 'trade_other')
            ->setData('used_in_forms', array('adminhtml_customer'))
            ->save();
    }

    $installer->endSetup();