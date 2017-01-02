<?php

    $installer = $this;
    $installer->startSetup();

    /* SETUP THE SALES REPRESENTATIVE */
    $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
    $setup->addAttribute('customer', 'age', array(
        'type' => 'select',
        'input' => 'int',
        'label' => 'Age',
        'global' => 1,
        'visible' => 1,
        'required' => 0,
        'user_defined' => 1,
        'default' => '0',
        'visible_on_front' => 0,
        'source' => 'profile/entity_salesrep',
    ));

    if (version_compare(Mage::getVersion(), '1.6.0', '<=')) {
        $customer = Mage::getModel('customer/customer');
        $attrSetId = $customer->getResource()->getEntityType()->getDefaultAttributeSetId();
        $setup->addAttributeToSet('customer', $attrSetId, 'General', 'age');
    }

    if (version_compare(Mage::getVersion(), '1.4.2', '>=')) {
        Mage::getSingleton('eav/config')
            ->getAttribute('customer', 'age')
            ->setData('used_in_forms', array('adminhtml_customer'))
            ->save();
    }

//$tablequote = $this->getTable('sales/quote');
//$installer->run("ALTER TABLE  $tablequote ADD  `customer_salesrep` INT NOT NULL");

    $installer->endSetup();