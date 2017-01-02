<?php

    $installer = $this;
    $installer->startSetup();

    /**
     * Setup the License Entity for tradies that signup, which will be used to validate trade number
     */
    $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
    $setup->addAttribute('customer', 'tradenumber', array(
        'type' => 'varchar',
        'input' => 'text',
        'label' => 'Trade Number',
        'global' => 1,
        'visible' => 1,
        'required' => 0,
        'user_defined' => 1,
        'default' => NULL,
        'visible_on_front' => 0,
    ));

    if (version_compare(Mage::getVersion(), '1.4.2', '>=')) {
        Mage::getSingleton('eav/config')
            ->getAttribute('customer', 'tradenumber')
            ->setData('used_in_forms', array('adminhtml_customer'))
            ->save();
    }
    $customer = Mage::getModel('customer/attribute')->loadByCode('customer', 'tradenumber');
    $forms = array('customer_account_edit', 'customer_account_create', 'adminhtml_customer', 'checkout_register');
    $customer->setData('used_in_forms', $forms);
    $customer->save();


    if (version_compare(Mage::getVersion(), '1.4.2', '>=')) {
        Mage::getSingleton('eav/config')
            ->getAttribute('customer', 'trade')
            ->setData('used_in_forms', array('adminhtml_customer'))
            ->save();
    }
    $customer = Mage::getModel('customer/attribute')->loadByCode('customer', 'trade');
    $forms = array('customer_account_edit', 'customer_account_create', 'adminhtml_customer', 'checkout_register');
    $customer->setData('used_in_forms', $forms);
    $customer->save();

    $installer->endSetup();