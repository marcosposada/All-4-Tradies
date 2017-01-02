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

class Appmerce_Eway_Model_Api_Rapid extends Appmerce_Eway_Model_Cc
{
    protected $_code = Appmerce_Eway_Model_Config::METHOD_RAPID;
    protected $_formBlockType = 'eway/form_rapid';

    // Magento features
    protected $_canSaveCc = true;
    protected $_canAuthorize = true;
    protected $_canCapture = true;
    protected $_canRefund = true;
    protected $_canRefundPartial = true;

    /**
     * Assign data to info model instance
     *
     * @param   mixed $data
     * @return  Mage_Payment_Model_Info
     */
    public function assignData($data)
    {
        parent::assignData($data);
        if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }

        $info = $this->getInfoInstance();
        $info->setOwner($data->getRapidOwner())->setCcLast4(substr($data->getRapidNumber(), -4))->setCcType($data->getRapidType());
        return $this;
    }

    /**
     * Prepare info instance for save
     *
     * @return Mage_Payment_Model_Abstract
     */
    public function prepareSave()
    {
        $info = $this->getInfoInstance();
        if ($this->_canSaveCc) {
            $info->setCcNumberEnc($info->encrypt($info->getRapidNumber()));
        }
        return $this;
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
     * Return order process instance
     *
     * @return Appmerce_Ogone_Model_Process
     */
    public function getProcess()
    {
        return Mage::getSingleton('eway/process');
    }

    /**
     * Get config action to process initialization
     *
     * @return string
     */
    public function getConfigPaymentAction()
    {
        $paymentAction = $this->getConfigData('payment_action');
        return empty($paymentAction) ? true : $paymentAction;
    }

    /**
     * Validate if payment is possible
     *  - check allowed currency codes
     *
     * @return bool
     */
    public function validate()
    {
        return $this;
    }

    /**
     * Refund specified amount for payment
     *
     * @param Varien_Object $payment
     * @param float $amount
     * @return Mage_Payment_Model_Abstract
     */
    public function refund(Varien_Object $payment, $amount)
    {
        if (!$this->canRefund()) {
            Mage::throwException(Mage::helper('eway')->__('Refund action is not available.'));
        }

        // Refund through eWAY
        if ($payment->getRefundTransactionId() && $amount > 0) {
            $order = $payment->getOrder();
            if ($order->getId()) {

                // Request
                $fields = $this->getRefundFields($order);
                try {
                    $response = $this->curlPostJson('DirectRefund', $fields, $order->getStoreId());
                }
                catch (Exception $e) {
                    Mage::throwException($e->getMessage());
                }

                // Debug
                if ($this->getConfigData('debug_flag')) {
                    Mage::getModel('eway/api_debug')->setDir('out')->setUrl('')->setInfo($fields)->save();
                    Mage::getModel('eway/api_debug')->setDir('in')->setUrl('')->setInfo($request)->save();
                }

                switch ($response->TransactionStatus) {
                    case '1' :
                    case 'true' :
                        break;

                    default :
                        Mage::throwException(Mage::helper('eway')->__('eWAY refund failed.'));
                }
            }
            else {
                Mage::throwException(Mage::helper('eway')->__('Invalid order for refund.'));
            }
        }
        else {
            Mage::throwException(Mage::helper('eway')->__('Invalid transaction for refund.'));
        }

        return $this;
    }

    /**
     * Generates array of fields for redirect form
     *
     * @return array
     */
    public function getRefundFields($order)
    {
        if (empty($order)) {
            if (!($order = $this->getOrder())) {
                return array();
            }
        }

        $storeId = $order->getStoreId();
        $paymentMethodCode = $order->getPayment()->getMethod();

        $refundFields = array();

        /**
         * Refund
         */
        $refundFields['Refund']['TotalAmount'] = substr(intval(($order->getBaseGrandTotal() * 100) + 0.5), 0, 10);
        $refundFields['Refund']['InvoiceNumber'] = substr($order->getIncrementId(), 0, 16);
        $refundFields['Refund']['InvoiceDescription'] = substr($this->getConfig()->getOrderDescription($order), 0, 64);
        $refundFields['Refund']['InvoiceReference'] = substr($order->getId(), 0, 50);
        $refundFields['Refund']['CurrencyCode'] = substr($this->getCurrencyCode(), 0, 3);
        $refundFields['Refund']['TransactionID'] = substr($order->getPayment()->getLastTransId(), 0, 8);

        return $refundFields;
    }

    /**
     * Authorize payment
     *
     * @param Varien_Object $payment
     * @param float $amount
     * @return Mage_Payment_Model_Abstract
     */
    public function authorize(Varien_Object $payment, $amount)
    {
        if ($amount <= 0) {
            Mage::throwException(Mage::helper('eway')->__('Invalid amount for capture.'));
        }

        $payment->setAmount($amount);
        $order = $payment->getOrder();

        // Request
        $fields = $this->getAccessFields($order);
        try {
            $response = $this->curlPostJson('CreateAccessCode', $fields, $order->getStoreId());
        }
        catch (Exception $e) {
            Mage::throwException($e->faultstring);
        }

        // Debug
        if ($this->getConfigData('debug_flag')) {
            Mage::getModel('eway/api_debug')->setDir('out')->setUrl('checkout/onepage')->setInfo($fields)->save();
            Mage::getModel('eway/api_debug')->setDir('in')->setUrl('checkout/onepage')->setInfo($response)->save();
        }

        // If we have a valid Access Code, we do a Curl Post for the
        // Card Data we already collected
        if (isset($response->AccessCode) && !empty($response->AccessCode)) {
            $accessCode = $response->AccessCode;

            /* save additional info into session, Magento don't save additional transaction info */
            $additionalInfo = isset($response->AccessCode) ? array('AccessCode' => $accessCode) : array();
            $additionalInfo = isset($response->Customer) ? array_merge($additionalInfo, Mage::helper('eway')->stdToArray($response->Customer, 'Customer')) : $additionalInfo;
            $additionalInfo = isset($response->Payment) ? array_merge($additionalInfo, Mage::helper('eway')->stdToArray($response->Payment, 'Payment')) : $additionalInfo;
            Mage::getSingleton('checkout/session')->setData('additionalInfo', $additionalInfo);

            $order->getPayment()->setAppmerceAccessCode($accessCode);
            $payment->setAdditionalInformation('eway_access_code', $accessCode);
            $payment->setAdditionalInformation('eway_form_action_url', $response->FormActionURL);

            // Don't add transaction
        }
        else {
            $note = Mage::helper('eway')->__('Failed to access eWAY. Please contact the merchant.') . ' ' . $this->buildNote($response);
            Mage::throwException($note);
        }

        return $this;
    }

    /**
     * Capture payment
     *
     * @param Varien_Object $payment
     * @param float $amount
     * @return Mage_Payment_Model_Abstract
     */
    public function capture(Varien_Object $payment, $amount)
    {
        if ($amount <= 0) {
            Mage::throwException(Mage::helper('eway')->__('Invalid amount for capture.'));
        }

        $payment->setAmount($amount);
        $order = $payment->getOrder();

        // Request
        $fields = $this->getAccessFields($order);
        try {
            $response = $this->curlPostJson('CreateAccessCode', $fields, $order->getStoreId());
        }
        catch (Exception $e) {
            Mage::throwException($e->getMessage());
        }

        // Debug
        if ($this->getConfigData('debug_flag')) {
            Mage::getModel('eway/api_debug')->setDir('out')->setUrl('checkout/onepage')->setInfo($fields)->save();
            Mage::getModel('eway/api_debug')->setDir('in')->setUrl('checkout/onepage')->setInfo($response)->save();
        }

        // If we have a valid Access Code, we do a Curl Post for the
        // Card Data we already collected
        if (isset($response->AccessCode) && !empty($response->AccessCode)) {
            $accessCode = $response->AccessCode;

            /* save additional info into session, Magento don't save additional transaction info */
            $additionalInfo = isset($response->AccessCode) ? array('AccessCode' => $accessCode) : array();
            $additionalInfo = isset($response->Customer) ? array_merge($additionalInfo, Mage::helper('eway')->stdToArray($response->Customer, 'Customer')) : $additionalInfo;
            $additionalInfo = isset($response->Payment) ? array_merge($additionalInfo, Mage::helper('eway')->stdToArray($response->Payment, 'Payment')) : $additionalInfo;
            Mage::getSingleton('checkout/session')->setData('additionalInfo', $additionalInfo);

            $order->getPayment()->setAppmerceAccessCode($accessCode);
            $payment->setAdditionalInformation('eway_access_code', $accessCode);
            $payment->setAdditionalInformation('eway_form_action_url', $response->FormActionURL);

            $this->_addTransaction($payment, $payment->getLastTransId(), Mage_Sales_Model_Order_Payment_Transaction::TYPE_PAYMENT, array('is_transaction_closed' => 1), $additionalInfo, "");
        }
        else {
            $note = Mage::helper('eway')->__('Failed to access eWAY. Please contact the merchant.') . ' ' . $this->buildNote($response);
            Mage::throwException($note);
        }

        return $this;
    }

    /**
     * Generates array of fields for redirect form
     *
     * @return array
     */
    public function getAccessFields($order)
    {
        if (empty($order)) {
            if (!($order = $this->getOrder())) {
                return array();
            }
        }

        $storeId = $order->getStoreId();
        $billingAddress = $order->getBillingAddress();
        $shippingAddress = $order->getShippingAddress();
        if (!$shippingAddress || !is_object($shippingAddress)) {
            $shippingAddress = $billingAddress;
        }
        $paymentMethodCode = $order->getPayment()->getMethod();

        $accessFields = array();

        /**
         * Customer
         */
        $accessFields['Customer']['Reference'] = substr($order->getCustomerId(), 0, 50);
        //$accessFields['Customer']['Title'] = substr('', 0, 5);
        $accessFields['Customer']['FirstName'] = substr($billingAddress->getFirstname(), 0, 50);
        $accessFields['Customer']['LastName'] = substr($billingAddress->getLastname(), 0, 50);
        if ($billingAddress->getCompany()) {
            $accessFields['Customer']['CompanyName'] = substr($billingAddress->getCompany(), 0, 50);
        }
        //$accessFields['Customer']['JobDescription'] = substr('', 0, 50);
        $accessFields['Customer']['Street1'] = substr($billingAddress->getStreet(1), 0, 50);
        if ($billingAddress->getStreet(2)) {
            $accessFields['Customer']['Street2'] = substr($billingAddress->getStreet(2), 0, 50);
        }
        $accessFields['Customer']['City'] = substr($billingAddress->getCity(), 0, 50);
        $accessFields['Customer']['State'] = substr($billingAddress->getRegion(), 0, 50);
        $accessFields['Customer']['PostalCode'] = substr($billingAddress->getPostcode(), 0, 30);
        $accessFields['Customer']['Country'] = strtolower($billingAddress->getCountry());
        $email = $billingAddress->getEmail();
        if (strlen($email <= 50)) {
            $accessFields['Customer']['Email'] = $email;
        }
        if ($billingAddress->getTelephone()) {
            //$accessFields['Customer']['Phone'] = substr(preg_replace('/[^+()0-9]/', '', $billingAddress->getTelephone()), 0, 32);
        }
        //$accessFields['Customer']['Mobile'] = substr(preg_replace('/[^+()0-9]/', '', ''), 0, 32);
        //$accessFields['Customer']['Comments'] = substr('', 0, 255);
        if ($billingAddress->getFax()) {
            //$accessFields['Customer']['Fax'] = substr(preg_replace('/[^+()0-9]/', '', $billingAddress->getFax()), 0, 32);
        }
        //$accessFields['Customer']['Website'] = substr('', 0, 512);

        /**
         * Shipping Address
         */
        $accessFields['ShippingAddress']['FirstName'] = substr($shippingAddress->getFirstname(), 0, 50);
        $accessFields['ShippingAddress']['LastName'] = substr($shippingAddress->getLastname(), 0, 50);
        $accessFields['ShippingAddress']['CompanyName'] = substr($shippingAddress->getCompany(), 0, 50);
        //$accessFields['Shipping']['JobDescription'] = substr('', 0, 50);
        $accessFields['ShippingAddress']['Street1'] = substr($shippingAddress->getStreet(1), 0, 50);
        if ($shippingAddress->getStreet(2)) {
            $accessFields['ShippingAddress']['Street2'] = substr($shippingAddress->getStreet(2), 0, 50);
        }
        $accessFields['ShippingAddress']['City'] = substr($shippingAddress->getCity(), 0, 50);
        $accessFields['ShippingAddress']['State'] = substr($shippingAddress->getRegion(), 0, 50);
        $accessFields['ShippingAddress']['PostalCode'] = substr($shippingAddress->getPostcode(), 0, 30);
        $accessFields['ShippingAddress']['Country'] = strtolower($shippingAddress->getCountry());
        $email = $shippingAddress->getEmail();
        if (strlen($email <= 50)) {
            $accessFields['ShippingAddress']['Email'] = $email;
        }
        if ($shippingAddress->getTelephone()) {
            //$accessFields['ShippingAddress']['Phone'] = substr(preg_replace('/[^+()0-9]/', '', $shippingAddress->getTelephone()), 0, 32);
        }
        if ($shippingAddress->getFax()) {
            //$accessFields['ShippingAddress']['Fax'] = substr(preg_replace('/[^+()0-9]/', '', $shippingAddress->getFax()), 0, 32);
        }

        /**
         * Shipping Method
         */
        $accessFields['ShippingMethod'] = substr(Appmerce_Eway_Model_Config::SHIPPING_METHOD_UNKNOWN, 0, 30);

        /**
         * Line items
         */
        foreach ($order->getAllItems() as $orderItem) {
            if ($orderItem->getParentItemId()) {
                continue;
            }
            $accessFields['Items']['SKU'] = substr($orderItem->getItemId(), 0, 12);
            $accessFields['Items']['Description'] = substr($orderItem->getName(), 0, 26);
            $accessFields['Items']['Quantity'] = substr(intval($orderItem->getQtyOrdered() + 0.5), 0, 6);
            $accessFields['Items']['UnitCost'] = substr(intval(($orderItem->getBasePriceInclTax() * 100) + 0.5), 0, 8);
        }

        /**
         * Options
         */
        //$accessFields['Options']['Value1'] = 'Option1';
        //$accessFields['Options']['Value2'] = 'Option2';
        //$accessFields['Options']['Value3'] = 'Option3';

        /**
         * Payment
         */
        $accessFields['Payment']['TotalAmount'] = substr(intval(($order->getBaseGrandTotal() * 100) + 0.5), 0, 10);
        $accessFields['Payment']['InvoiceNumber'] = substr($order->getIncrementId(), 0, 16);
        $accessFields['Payment']['InvoiceDescription'] = substr($this->getConfig()->getOrderDescription($order), 0, 64);
        $accessFields['Payment']['InvoiceReference'] = substr($order->getId(), 0, 50);
        $accessFields['Payment']['CurrencyCode'] = substr($this->getCurrencyCode(), 0, 3);

        /**
         * Other
         */
        $accessFields['CustomerIP'] = substr(Mage::helper('eway')->getRealIpAddr(), 0, 50);
        $accessFields['Method'] = Appmerce_Eway_Model_Config::METHOD_PROCESS_PAYMENT;
        $accessFields['TransactionType'] = Appmerce_Eway_Model_Config::TRANSACTION_TYPE_PURCHASE;
        //$accessFields['DeviceID'] = substr('', 0, 50);
        //$accessFields['PartnerID'] = substr('', 0, 50);
        $accessFields['RedirectUrl'] = substr($this->getConfig()->getRapidUrl('redirect', $storeId, true), 0, 512);
        $accessFields['CustomerReadOnly'] = true;
        //$accessFields['CustomView'] = true;
        $accessFields['VerifyCustomerPhone'] = false;
        $accessFields['VerifyCustomerEmail'] = true;

        return $accessFields;
    }

    /**
     * Generates array of card fields
     *
     * @return array
     */
    public function getCardFields($order, $accessCode, $response = false)
    {
        if (empty($order)) {
            if (!($order = $this->getOrder())) {
                return array();
            }
        }

        $cardFields = array();
        $cardFields['EWAY_ACCESSCODE'] = $accessCode;
        $cardFields['EWAY_PAYMENTTYPE'] = Appmerce_Eway_Model_Config::PAYMENT_TYPE_CREDIT_CARD;

        // Expiration date mm/yy
        $month = $order->getPayment()->getCcExpMonth();
        $mm = (string)$month < 10 ? '0' . $month : $month;
        $yy = substr((string)$order->getPayment()->getCcExpYear(), 2, 2);

        $cardFields['EWAY_CARDNAME'] = $order->getPayment()->getCcOwner();
        $cardFields['EWAY_CARDNUMBER'] = $order->getPayment()->getCcNumber();
        $cardFields['EWAY_CARDEXPIRYMONTH'] = $mm;
        $cardFields['EWAY_CARDEXPIRYYEAR'] = $yy;

        // Only required when TransactionType is Purchase or not sent.
        $cardFields['EWAY_CARDCVN'] = $order->getPayment()->getCcCid();

        return $cardFields;
    }

    /**
     * Build $note with error message
     */
    public function buildNote($response, &$fraud = false)
    {
        $note = '';

        // Main response message
        if (isset($response->ResponseMessage)) {
            $note .= Mage::helper('eway')->__('Response Message: %s (%s).', $response->ResponseMessage, $response->ResponseCode);
        }

        // Error messages
        if (isset($response->Errors) && !empty($response->Errors)) {
            $note = Mage::helper('eway')->__('Errors: %s.', $response->Errors);
            $note .= '<br />' . Mage::helper('eway')->__('Documentation: %s.', 'http://www.apihub.com/eway/api/eway-rapid-31-api/docs/responses');

            // Count fraud
            foreach (explode(',', $response->Errors) as $code) {
                if (substr($code, 0, 1) == 'F') {
                    ++$fraud;
                }
            }
        }

        // Beagle score
        if (isset($response->BeagleScore)) {
            $note .= '<br />' . Mage::helper('eway')->__('Beagle Score: %s', $response->BeagleScore);
        }

        return $note;
    }

    /**
     * Add payment transaction
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @param string $transactionId
     * @param string $transactionType
     * @param array $transactionDetails
     * @param array $transactionAdditionalInfo
     * @return null|Mage_Sales_Model_Order_Payment_Transaction
     */
    protected function _addTransaction(Mage_Sales_Model_Order_Payment $payment, $transactionId, $transactionType, array $transactionDetails = array(), array $transactionAdditionalInfo = array(), $message = false)
    {
        $message = $message . '<br />';
        $payment->setTransactionId($transactionId);

        $transaction = $payment->addTransaction($transactionType, null, false, $message);
        $transaction->setAdditionalInformation(Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS, $transactionAdditionalInfo);

        $payment->unsLastTransId();

        /**
         * It for self using
         */
        $transaction->setMessage($message);

        return $transaction;
    }

    /**
     * Reset assigned data in payment info model
     *
     * @param Mage_Payment_Model_Info
     * @return Mage_Paygate_Model_Authorizenet
     */
    private function _clearAssignedData($payment)
    {
        //$payment->setCcOwner(null)->setCcNumber(null)->setCcCid(null)->setCcExpMonth(null)->setCcExpYear(null)->setCcSsIssue(null)->setCcSsStartMonth(null)->setCcSsStartYear(null);
        return $this;
    }

}
