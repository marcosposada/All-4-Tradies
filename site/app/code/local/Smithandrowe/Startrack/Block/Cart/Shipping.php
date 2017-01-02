<?php
class Smithandrowe_Startrack_Block_Cart_Shipping extends Mage_Checkout_Block_Cart_Shipping
{
	public function getCityActive()
	{
		return (bool)Mage::getStoreConfig('carriers/dhl/active')
		|| (bool)Mage::getStoreConfig('carriers/dhlint/active')
		|| (bool)Mage::getStoreConfig('carriers/smithandrowe_startrack_express/active');
	}
}