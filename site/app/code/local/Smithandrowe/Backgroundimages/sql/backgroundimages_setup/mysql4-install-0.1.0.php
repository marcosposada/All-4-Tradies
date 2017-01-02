<?php 

$installer = $this;
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();
 
$installer->run('
  ALTER TABLE  `' . Mage::getConfig()->getTablePrefix() . 'cms_page` ADD  `background` VARCHAR( 255 ) NULL;
');

$installer->addAttribute('catalog_category', 'background_image', array(
    'group' => 'Background Image',
    'input' => 'image',
    'type' => 'varchar',
    'label' => 'Background Image',
    'backend' => 'catalog/category_attribute_backend_image',
    'visible' => 1,
    'required' => 0,
    'user_defined' => 1,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));

$installer->endSetup();