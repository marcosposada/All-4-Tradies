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

class Appmerce_Eway_Block_Placement extends Mage_Core_Block_Template
{
    public function __construct()
    {
    }

    /**
     * Return checkout session
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Return payment API model
     *
     * @return Appmerce_Eway_Model_Api_Paypal
     */
    protected function getApi()
    {
        return Mage::getSingleton('eway/api_paypal');
    }

    /**
     * Return order instance by lastRealOrderId
     *
     * @return Mage_Sales_Model_Order
     */
    protected function _getOrder()
    {
        if ($this->getOrder()) {
            $order = $this->getOrder();
        }
        elseif ($this->getCheckout()->getLastRealOrderId()) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($this->getCheckout()->getLastRealOrderId());
        }

        return $order;
    }

    /**
     * Return placement form fields
     *
     * @return array
     */
    public function getFormData()
    {
        $order = $this->_getOrder();
        $additionalInfo = Mage::getSingleton('checkout/session')->getData('additionalInfo');
        return $order->getPayment()->getMethodInstance()->getCardFields($this->_getOrder(), $additionalInfo['AccessCode']);
    }

    /**
     * Return gateway path from admin settings
     *
     * @return string
     */
    public function getFormAction()
    {
        $additionalInfo = Mage::getSingleton('checkout/session')->getData('additionalInfo');
        return $additionalInfo['FormActionURL'];
    }

}
