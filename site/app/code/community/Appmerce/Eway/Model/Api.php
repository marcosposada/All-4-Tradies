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

class Appmerce_Eway_Model_Api extends Mage_Payment_Model_Method_Abstract
{
    protected $_formBlockType = 'eway/form';
    protected $_infoBlockType = 'eway/info';

    // Magento features
    protected $_isGateway = false;
    protected $_canOrder = false;
    protected $_canAuthorize = false;
    protected $_canCapture = false;
    protected $_canCapturePartial = false;
    protected $_canRefund = false;
    protected $_canRefundInvoicePartial = false;
    protected $_canVoid = false;
    protected $_canUseInternal = false;
    protected $_canUseCheckout = true;
    protected $_canUseForMultishipping = false;
    protected $_isInitializeNeeded = true;
    protected $_canFetchTransactionInfo = false;
    protected $_canReviewPayment = false;
    protected $_canCreateBillingAgreement = false;
    protected $_canManageRecurringProfiles = false;

    // Restrictions
    protected $_allowCurrencyCode = array(
        'GBP',
        'AUD',
        'NZD',
        'USD',
    );

    public function __construct()
    {
        $this->_config = Mage::getSingleton('eway/config');
        return $this;
    }

    /**
     * Return configuration instance
     *
     * @return Appmerce_Eway_Model_Config
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * Validate if payment is possible
     *  - check allowed currency codes
     *
     * @return bool
     */
    public function validate()
    {
        parent::validate();
        $currency_code = $this->getCurrencyCode();
        if (!empty($this->_allowCurrencyCode) && !in_array($currency_code, $this->_allowCurrencyCode)) {
            $errorMessage = Mage::helper('eway')->__('Selected currency (%s) is not compatible with this payment method.', $currency_code);
            Mage::throwException($errorMessage);
        }
        return $this;
    }

    /**
     * Get gateway Url
     *
     * @return string
     */
    public function getGatewayUrl($type)
    {
        if ($this->getConfigData('test_flag')) {
            $base_url = Appmerce_Eway_Model_Config::BASE_URL_SANDBOX;
        }
        else {
            $base_url = Appmerce_Eway_Model_Config::BASE_URL;
        }

        $gateways = $this->getConfig()->getGateways();
        return $base_url . $gateways[$type];
    }

    /**
     * Generates array of card fields
     *
     * @return array
     */
    public function getCardFields($order, $accessCode)
    {
        if (empty($order)) {
            if (!($order = $this->getOrder())) {
                return array();
            }
        }

        $cardFields = array();
        $cardFields['EWAY_ACCESSCODE'] = $accessCode;
        $cardFields['EWAY_PAYMENTTYPE'] = Appmerce_Eway_Model_Config::PAYMENT_TYPE_PAYPAL;
        return $cardFields;
    }

    /**
     * Post with CURL and return response
     *
     * @param $postUrl The URL with ?key=value
     * @param $postXml string XML message
     * @return reponse XML Object
     */
    public function curlPostJson($gateway, $fields = array(), $storeId = false)
    {
        Mage::log('Got function');
        $ch = curl_init();

        $json = json_encode($fields);
        $gate = $this->getGatewayUrl($gateway);
        $user = $this->getConfigData('username', $storeId);
        $pass = $this->getConfigData('password', $storeId);
        Mage::log('User: '.$user);
        curl_setopt($ch, CURLOPT_URL, $gate);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, "$user:$pass");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, "$json");

        $response = curl_exec($ch);
        curl_close($ch);
        Mage::log('User: '.$user);
        return json_decode($response);
    }

    /**
     * Decide currency code type
     *
     * @return string
     */
    public function getCurrencyCode()
    {
        return Mage::app()->getStore()->getBaseCurrencyCode();
    }

}
