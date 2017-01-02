<?php
class Smithandrowe_Startrack_Model_Services
{
	public function toOptionArray() 
	{
		return array(
			array('value' => 'EXP', 'label' => Mage::helper('smithandrowe_startrack')->__('ROAD EXPRESS (EXP)')),
			array('value' => '1KN', 'label' => Mage::helper('smithandrowe_startrack')->__('1 KG NATIONWIDE (1KN)')),
			array('value' => '3KN', 'label' => Mage::helper('smithandrowe_startrack')->__('3 KG NATIONWIDE (3KN)')),
			array('value' => '5KN', 'label' => Mage::helper('smithandrowe_startrack')->__('5 KG NATIONWIDE (5KN)')),
			array('value' => 'TSE', 'label' => Mage::helper('smithandrowe_startrack')->__('TRADE SHOW EXPRESS (TSE)')),
			array('value' => 'RET', 'label' => Mage::helper('smithandrowe_startrack')->__('ROAD EXPRESS T\'GATE (RET)')),
			array('value' => 'RE2', 'label' => Mage::helper('smithandrowe_startrack')->__('ROAD EXPRESS 2MEN (RE2)')),
			array('value' => 'ITL', 'label' => Mage::helper('smithandrowe_startrack')->__('INTL EXPRESS FREIGHT (ITL)')),
			array('value' => 'LO2', 'label' => Mage::helper('smithandrowe_startrack')->__('LOCAL OVERNIGHT 2MEN (LO2)')),
			array('value' => 'LOT', 'label' => Mage::helper('smithandrowe_startrack')->__('LOCAL OVERNIGHT TAIL (LOT)')),
			array('value' => 'PAC', 'label' => Mage::helper('smithandrowe_startrack')->__('PRIORITY AIR SERVICE (PAC)')),
			array('value' => 'SAT', 'label' => Mage::helper('smithandrowe_startrack')->__('SATURDAY DELIVERY (SAT)')),
			array('value' => 'SDA', 'label' => Mage::helper('smithandrowe_startrack')->__('SAMEDAY STARTRACK CR (SDA)')),
			array('value' => 'IDS', 'label' => Mage::helper('smithandrowe_startrack')->__('INT\'L DOCUMENT EXP (IDS)')),
		);
	} 
}