<?php


    $installer = new Mage_Customer_Model_Entity_Setup('core_setup');

    $installer->startSetup();

    $vCustomerEntityType = $installer->getEntityTypeId('customer');
    $vCustAttributeSetId = $installer->getDefaultAttributeSetId($vCustomerEntityType);
    $vCustAttributeGroupId = $installer->getDefaultAttributeGroupId($vCustomerEntityType, $vCustAttributeSetId);

    $installer->addAttribute('customer', 'business_abn', array(
        'label' => 'Business ABN',
        'input' => 'text',
        'type' => 'varchar',
        'forms' => array('customer_account_edit', 'customer_account_create', 'adminhtml_customer', 'checkout_register'),
        'required' => 0,
        'user_defined' => 1,
    ));

    $installer->addAttributeToGroup($vCustomerEntityType, $vCustAttributeSetId, $vCustAttributeGroupId, 'business_abn', 0);

    $oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'business_abn');
    $oAttribute->setData('used_in_forms', array('customer_account_edit', 'customer_account_create', 'adminhtml_customer', 'checkout_register'));
    $oAttribute->save();

    $installer->endSetup();

?>