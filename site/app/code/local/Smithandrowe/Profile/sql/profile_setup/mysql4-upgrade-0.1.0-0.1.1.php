<?php

    $installer = $this;
    $installer->startSetup();

    /**
     * Setup the License Entity for tradies that signup, which will be used to validate trade number
     */
    $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
    $setup->addAttribute('customer', 'license_entity', array(
        'type' => 'varchar',
        'input' => 'text',
        'label' => 'License Entity',
        'global' => 1,
        'visible' => 1,
        'required' => 0,
        'user_defined' => 1,
        'default' => '0',
        'visible_on_front' => 0,
    ));

    if (version_compare(Mage::getVersion(), '1.4.2', '>=')) {
        Mage::getSingleton('eav/config')
            ->getAttribute('customer', 'license_entity')
            ->setData('used_in_forms', array('adminhtml_customer'))
            ->save();
    }
    $customer = Mage::getModel('customer/attribute')->loadByCode('customer', 'license_entity');
    $forms = array('customer_account_edit', 'customer_account_create', 'adminhtml_customer', 'checkout_register');
    $customer->setData('used_in_forms', $forms);
    $customer->save();

    /**
     * Create the 'trade_years_registered' which is to determine how long they have been a trade member for
     * This will be used for verification
     */

    $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
    $setup->addAttribute('customer', 'trade_year_registered', array(
        'type' => 'int',
        'input' => 'select',
        'label' => 'Trade Years Registered',
        'global' => 1,
        'visible' => 1,
        'required' => 0,
        'user_defined' => 1,
        'default' => '0',
        'visible_on_front' => 0,
        'source' => 'profile/entity_tradeYearRegistered',
    ));


    if (version_compare(Mage::getVersion(), '1.4.2', '>=')) {
        Mage::getSingleton('eav/config')
            ->getAttribute('customer', 'trade_year_registered')
            ->setData('used_in_forms', array('adminhtml_customer'))
            ->save();
    }
    $customer = Mage::getModel('customer/attribute')->loadByCode('customer', 'trade_year_registered');
    $forms = array('customer_account_edit', 'customer_account_create', 'adminhtml_customer', 'checkout_register');
    $customer->setData('used_in_forms', $forms);
    $customer->save();

    /**
     * Add the attribute to determine what year an apprentice is on
     */

    $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
    $setup->addAttribute('customer', 'apprentice_year', array(
        'type' => 'int',
        'input' => 'select',
        'label' => 'Apprentice Year',
        'global' => 1,
        'visible' => 1,
        'required' => 0,
        'user_defined' => 1,
        'default' => '0',
        'visible_on_front' => 1,
        'source' => 'profile/entity_apprenticeYears',
    ));

    if (version_compare(Mage::getVersion(), '1.4.2', '>=')) {
        Mage::getSingleton('eav/config')
            ->getAttribute('customer', 'apprentice_year')
            ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create'))
            ->save();
    }

    /**
     * Setup 'Name of Employer' so customers enter the employer which is who they work for
     */
    $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
    $setup->addAttribute('customer', 'employer_name', array(
        'type' => 'varchar',
        'input' => 'text',
        'label' => 'Name of Employer',
        'global' => 1,
        'visible' => 1,
        'required' => 0,
        'user_defined' => 1,
        'default' => '0',
        'visible_on_front' => 1,
    ));

    if (version_compare(Mage::getVersion(), '1.4.2', '>=')) {
        Mage::getSingleton('eav/config')
            ->getAttribute('customer', 'employer_name')
            ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create'))
            ->save();

    }
    $customer = Mage::getModel('customer/attribute')->loadByCode('customer', 'employer_name');
    $forms = array('customer_account_edit', 'customer_account_create', 'adminhtml_customer', 'checkout_register');
    $customer->setData('used_in_forms', $forms);
    $customer->save();


    /**
     * Add the attribute 'Business Number' for tradies and employees
     */

    $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
    $setup->addAttribute('customer', 'business_mobile', array(
        'type' => 'varchar',
        'input' => 'text',
        'label' => 'Business Mobile',
        'global' => 1,
        'visible' => 1,
        'required' => 0,
        'user_defined' => 1,
        'default' => '0',
        'visible_on_front' => 0,
    ));

    if (version_compare(Mage::getVersion(), '1.4.2', '>=')) {
        Mage::getSingleton('eav/config')
            ->getAttribute('customer', 'business_mobile')
            ->setData('used_in_forms', array('adminhtml_customer'))
            ->save();

    }
    $customer = Mage::getModel('customer/attribute')->loadByCode('customer', 'business_mobile');
    $forms = array('customer_account_edit', 'customer_account_create', 'adminhtml_customer', 'checkout_register');
    $customer->setData('used_in_forms', $forms);
    $customer->save();

    /**
     * Add the attribute 'Income' for tradies and employees
     */

    $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
    $setup->addAttribute('customer', 'income', array(
        'type' => 'int',
        'input' => 'select',
        'label' => 'Income',
        'global' => 1,
        'visible' => 1,
        'required' => 0,
        'user_defined' => 1,
        'default' => '0',
        'visible_on_front' => 1,
        'source' => 'profile/entity_income',
    ));

    if (version_compare(Mage::getVersion(), '1.4.2', '>=')) {
        Mage::getSingleton('eav/config')
            ->getAttribute('customer', 'income')
            ->setData('used_in_forms', array('adminhtml_customer'))
            ->save();

    }
    $customer = Mage::getModel('customer/attribute')->loadByCode('customer', 'income');
    $forms = array('customer_account_edit', 'customer_account_create', 'adminhtml_customer', 'checkout_register');
    $customer->setData('used_in_forms', $forms);
    $customer->save();

    /**
     * Add the attribute 'Income' for tradies and employees
     */

    $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
    $setup->addAttribute('customer', 'occupation', array(
        'type' => 'int',
        'input' => 'select',
        'label' => 'Occupation',
        'global' => 1,
        'visible' => 1,
        'required' => 0,
        'user_defined' => 1,
        'default' => '0',
        'visible_on_front' => 1,
        'source' => 'profile/entity_occupation',
    ));

    if (version_compare(Mage::getVersion(), '1.4.2', '>=')) {
        Mage::getSingleton('eav/config')
            ->getAttribute('customer', 'occupation')
            ->setData('used_in_forms', array('adminhtml_customer'))
            ->save();

    }
    $customer = Mage::getModel('customer/attribute')->loadByCode('customer', 'occupation');
    $forms = array('customer_account_edit', 'customer_account_create', 'adminhtml_customer', 'checkout_register');
    $customer->setData('used_in_forms', $forms);
    $customer->save();

    /**
     * Add the attribute 'Income' for tradies and employees
     */

    $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
    $setup->addAttribute('customer', 'nature_organisation', array(
        'type' => 'int',
        'input' => 'select',
        'label' => 'Nature of Organisation',
        'global' => 1,
        'visible' => 1,
        'required' => 0,
        'user_defined' => 1,
        'default' => '0',
        'visible_on_front' => 1,
        'source' => 'profile/entity_natureOrganisation',
    ));

    if (version_compare(Mage::getVersion(), '1.4.2', '>=')) {
        Mage::getSingleton('eav/config')
            ->getAttribute('customer', 'income')
            ->setData('used_in_forms', array('adminhtml_customer'))
            ->save();

    }
    $customer = Mage::getModel('customer/attribute')->loadByCode('customer', 'nature_organisation');
    $forms = array('customer_account_edit', 'customer_account_create', 'adminhtml_customer', 'checkout_register');
    $customer->setData('used_in_forms', $forms);
    $customer->save();


    /**
     * Add the attribute 'Business Number' for tradies and employees
     */

    $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
    $setup->addAttribute('customer', 'office_number', array(
        'type' => 'varchar',
        'input' => 'text',
        'label' => 'Office Number',
        'global' => 1,
        'visible' => 1,
        'required' => 0,
        'user_defined' => 1,
        'default' => '0',
        'visible_on_front' => 0,
    ));

    if (version_compare(Mage::getVersion(), '1.4.2', '>=')) {
        Mage::getSingleton('eav/config')
            ->getAttribute('customer', 'office_number')
            ->setData('used_in_forms', array('adminhtml_customer'))
            ->save();

    }
    $customer = Mage::getModel('customer/attribute')->loadByCode('customer', 'officer_number');
    $forms = array('customer_account_edit', 'customer_account_create', 'adminhtml_customer', 'checkout_register');
    $customer->setData('used_in_forms', $forms);
    $customer->save();

    /**
     * Add the attribute 'Business Number' for tradies and employees
     */

    $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
    $setup->addAttribute('customer', 'office_fax', array(
        'type' => 'varchar',
        'input' => 'text',
        'label' => 'Office Fax',
        'global' => 1,
        'visible' => 1,
        'required' => 0,
        'user_defined' => 1,
        'default' => '0',
        'visible_on_front' => 0,
    ));

    if (version_compare(Mage::getVersion(), '1.4.2', '>=')) {
        Mage::getSingleton('eav/config')
            ->getAttribute('customer', 'office_fax')
            ->setData('used_in_forms', array('adminhtml_customer'))
            ->save();

    }
    $customer = Mage::getModel('customer/attribute')->loadByCode('customer', 'office_fax');
    $forms = array('customer_account_edit', 'customer_account_create', 'adminhtml_customer', 'checkout_register');
    $customer->setData('used_in_forms', $forms);
    $customer->save();

//$tablequote = $this->getTable('sales/quote');
//$installer->run("ALTER TABLE  $tablequote ADD  `customer_salesrep` INT NOT NULL");

    $installer->endSetup();