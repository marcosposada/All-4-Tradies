<?php

    $installer = $this;
    $installer->startSetup();

    $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
    $setup->addAttribute('customer', 'account_type', array(
        'type' => 'int',
        'input' => 'select',
        'label' => 'Type of Account',
        'global' => 1,
        'visible' => 1,
        'required' => 0,
        'user_defined' => 1,
        'default' => '0',
        'visible_on_front' => 1,
        'source' => 'profile/entity_accountType',
    ));
    if (version_compare(Mage::getVersion(), '1.4.2', '>=')) {
        Mage::getSingleton('eav/config')
            ->getAttribute('customer', 'account_type')
            ->setData('used_in_forms', array('adminhtml_customer'))
            ->save();
    }

    $installer->endSetup();