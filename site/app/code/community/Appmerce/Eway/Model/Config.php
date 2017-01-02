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

class Appmerce_Eway_Model_Config extends Mage_Payment_Model_Config
{
    // Method types
    const METHOD_RAPID = 'eway_rapid';
    const METHOD_REDIRECT = 'eway_redirect';
    const METHOD_PAYPAL = 'eway_paypal';
    const METHOD_MASTERPASS = 'eway_masterpass';

    const PAYMENT_SERVICES_PATH = 'payment_services/appmerce_eway/';
    const API_CONTROLLER_PATH = 'eway/api/';
    const MASTERPASS_CONTROLLER_PATH = 'eway/masterpass/';
    const PAYPAL_CONTROLLER_PATH = 'eway/paypal/';
    const RAPID_CONTROLLER_PATH = 'eway/rapid/';

    // Gateway base URLs
    const BASE_URL = 'https://api.ewaypayments.com';
    const BASE_URL_SANDBOX = 'https://api.sandbox.ewaypayments.com';

    // Default order statuses
    const DEFAULT_STATUS_PENDING = 'pending';
    const DEFAULT_STATUS_PENDING_PAYMENT = 'pending_payment';
    const DEFAULT_STATUS_PROCESSING = 'processing';

    // Source model Appmerce_Eway_Model_Source_Cardsecurity
    const SECURITY_STANDARD = 'standard';
    const SECURITY_CVN = 'cvn';
    const SECURITY_BEAGLE = 'beagle';

    // Source Model Appmerce_Eway_Model_Source_Interfacelanguage
    const LANGUAGE_EN = 'EN';
    const LANGUAGE_ES = 'ES';
    const LANGUAGE_FR = 'FR';
    const LANGUAGE_DE = 'DE';
    const LANGUAGE_NL = 'NL';

    // Methods
    const METHOD_PROCESS_PAYMENT = 'ProcessPayment';
    const METHOD_CREATE_TOKEN_CUSTOMER = 'CreateTokenCustomer';
    const METHOD_UPDATE_TOKEN_CUSTOMER = 'UpdateTokenCustomer';
    const METHOD_TOKEN_PAYMENT = 'TokenPayment';

    // Transaction Types
    const TRANSACTION_TYPE_PURCHASE = 'Purchase';
    const TRANSACTION_TYPE_MOTO = 'MOTO';
    const TRANSACTION_TYPE_RECURRING = 'Recurring';

    // Payment Types
    const PAYMENT_TYPE_CREDIT_CARD = 'Credit Card';
    const PAYMENT_TYPE_PAYPAL = 'PayPal';
    const PAYMENT_TYPE_MASTERPASS = 'MasterPass';

    // Other
    const SHIPPING_METHOD_UNKNOWN = 'Unknown';

    /**
     * Get store configuration
     */
    public function getPaymentConfigData($method, $key, $storeId = null)
    {
        return Mage::getStoreConfig('payment/' . $method . '/' . $key, $storeId);
    }

    /**
     * Return checkout session
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Return Eway cards
     */
    public function getEwayCards()
    {
        return array(
            'VI' => 'Visa',
            'MC' => 'MasterCard',
            'AE' => 'American Express',
            'JCB' => 'JCB',
            'OT' => 'Diners Club'
        );
    }

    /**
     * Return gateways
     */
    public function getGateways()
    {
        return array(
            'CreateAccessCode' => '/CreateAccessCode.json',
            'CreateAccessCodeShared' => '/CreateAccessCodeShared.json',
            'DirectRefund' => '/DirectRefund.json',
            'GetAccessCodeResult' => '/GetAccessCodeResult.json'
        );
    }

    /**
     * Return order description
     *
     * @param Mage_Sales_Model_Order
     * @return string
     */
    public function getOrderDescription($order)
    {
        return Mage::helper('eway')->__('Order %s', $order->getIncrementId());
    }

    /**
     * Get order statuses
     */
    public function getOrderStatus($code)
    {
        $status = $this->getPaymentConfigData($code, 'order_status');
        if (empty($status)) {
            $status = self::DEFAULT_STATUS_PENDING;
        }
        return $status;
    }

    public function getPendingStatus($code)
    {
        $status = $this->getPaymentConfigData($code, 'pending_status');
        if (empty($status)) {
            $status = self::DEFAULT_STATUS_PENDING_PAYMENT;
        }
        return $status;
    }

    public function getProcessingStatus($code)
    {
        $status = $this->getPaymentConfigData($code, 'processing_status');
        if (empty($status)) {
            $status = self::DEFAULT_STATUS_PROCESSING;
        }
        return $status;
    }

    /**
     * Return URLs
     */
    public function getApiUrl($key, $storeId = null, $noSid = false)
    {
        return Mage::getUrl(self::API_CONTROLLER_PATH . $key, array(
            '_store' => $storeId,
            '_nosid' => $noSid,
            '_secure' => true
        ));
    }

    public function getMasterpassUrl($key, $storeId = null, $noSid = false)
    {
        return Mage::getUrl(self::MASTERPASS_CONTROLLER_PATH . $key, array(
            '_store' => $storeId,
            '_nosid' => $noSid,
            '_secure' => true
        ));
    }

    public function getPaypalUrl($key, $storeId = null, $noSid = false)
    {
        return Mage::getUrl(self::PAYPAL_CONTROLLER_PATH . $key, array(
            '_store' => $storeId,
            '_nosid' => $noSid,
            '_secure' => true
        ));
    }

    public function getRapidUrl($key, $storeId = null, $noSid = false)
    {
        return Mage::getUrl(self::RAPID_CONTROLLER_PATH . $key, array(
            '_store' => $storeId,
            '_nosid' => $noSid,
            '_secure' => true
        ));
    }

}
