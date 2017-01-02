<?php
/**
 * Appmerce - Applications for Ecommerce
 * http://ww.appmerce.com
 *
 * @extension   eWAY Rapid API 3.1
 * @type        Payment method
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Magento
 * @package     Appmerce_Eway
 * @copyright   Copyright (c) 2011-2014 Appmerce (http://www.appmerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Appmerce_Eway_Block_Review_Rapid extends Mage_Checkout_Block_Onepage_Abstract
{
    protected function _construct()
    {
        parent::_construct();
    }
	
    /**
     * Get AccessCods code
     *
     * @return string|null
     */    
	public function getAccessCode(){
		return Mage::helper('eway')->getOrderAccessCode();
    }
    

    /**
     * Get FormActionURL
     *
     * @return string|null
     */
    public function getFormActionURL(){
    	return Mage::helper('eway')->getFormActionURL();
    }    
}
