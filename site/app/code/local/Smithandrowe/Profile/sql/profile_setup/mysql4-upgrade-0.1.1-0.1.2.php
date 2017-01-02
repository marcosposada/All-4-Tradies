<?php

    $name = 'business_abn';
    $oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', $name);
    $oAttribute->setData(
        'used_in_forms',
        array('customer_account_edit', 'customer_account_create', 'adminhtml_customer')
    );
    $oAttribute->save();
?>