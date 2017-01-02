<?php
class Smithandrowe_Startrack_Model_States
{
	public function toOptionArray()
	{
		return array(
			array('value' => 'ACT', 'label' => Mage::helper('smithandrowe_startrack')->__('Australian Capital Territory')),
			array('value' => 'NSW', 'label' => Mage::helper('smithandrowe_startrack')->__('New South Wales')),
			array('value' => 'QLD', 'label' => Mage::helper('smithandrowe_startrack')->__('Queensland')),
			array('value' => 'SA', 'label' => Mage::helper('smithandrowe_startrack')->__('South Australia')),
			array('value' => 'TAS', 'label' => Mage::helper('smithandrowe_startrack')->__('Tasmania')),
			array('value' => 'VIC', 'label' => Mage::helper('smithandrowe_startrack')->__('Victoria')),
			array('value' => 'WA', 'label' => Mage::helper('smithandrowe_startrack')->__('Western Australia')),
		);
	}
}