<?php

    $installer = $this;
    $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
    $installer->startSetup();

    $installer->run('
  ALTER TABLE  `' . Mage::getConfig()->getTablePrefix() . 'cms_page` ADD  `promo` VARCHAR( 255 ) NULL;
');

    $installer->endSetup();