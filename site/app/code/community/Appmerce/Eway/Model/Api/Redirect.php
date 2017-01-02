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

class Appmerce_Eway_Model_Api_Redirect extends Appmerce_Eway_Model_Api
{
    protected $_code = Appmerce_Eway_Model_Config::METHOD_REDIRECT;

    /**
     * Get redirect URL after placing order
     *
     * @return string
     */
    public function getOrderPlaceRedirectUrl()
    {
        return $this->getConfig()->getApiUrl('placement');
    }

    /**
     * Generates array of fields for redirect form
     * @see http://www.apihub.com/eway/api/eway-rapid-31-api/docs/reference/responsive-shared-page
     *
     * @return array
     */
    public function getRedirectFields($order)
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

        $redirectFields = array();

        /**
         * Customer
         */
        $redirectFields['Customer']['Reference'] = substr($order->getCustomerId(), 0, 50);
        //$redirectFields['Customer']['Title'] = substr('', 0, 5);
        $redirectFields['Customer']['FirstName'] = substr($billingAddress->getFirstname(), 0, 50);
        $redirectFields['Customer']['LastName'] = substr($billingAddress->getLastname(), 0, 50);
        if ($billingAddress->getCompany()) {
            $redirectFields['Customer']['CompanyName'] = substr($billingAddress->getCompany(), 0, 50);
        }
        //$redirectFields['Customer']['JobDescription'] = substr('', 0, 50);
        $redirectFields['Customer']['Street1'] = substr($billingAddress->getStreet(1), 0, 50);
        if ($billingAddress->getStreet(2)) {
            $redirectFields['Customer']['Street2'] = substr($billingAddress->getStreet(2), 0, 50);
        }
        $redirectFields['Customer']['City'] = substr($billingAddress->getCity(), 0, 50);
        $redirectFields['Customer']['State'] = substr($billingAddress->getRegion(), 0, 50);
        $redirectFields['Customer']['PostalCode'] = substr($billingAddress->getPostcode(), 0, 30);
        $redirectFields['Customer']['Country'] = strtolower($billingAddress->getCountry());
        $email = $billingAddress->getEmail();
        if (strlen($email <= 50)) {
            $redirectFields['Customer']['Email'] = $email;
        }
        if ($billingAddress->getTelephone()) {
            //$redirectFields['Customer']['Phone'] = substr(preg_replace('/[^+()0-9]/', '', $billingAddress->getTelephone()), 0, 32);
        }
        //$redirectFields['Customer']['Mobile'] = substr(preg_replace('/[^+()0-9]/', '', ''), 0, 32);
        //$redirectFields['Customer']['Comments'] = substr('', 0, 255);
        if ($billingAddress->getFax()) {
            //$redirectFields['Customer']['Fax'] = substr(preg_replace('/[^+()0-9]/', '', $billingAddress->getFax()), 0, 32);
        }
        //$redirectFields['Customer']['Website'] = substr('', 0, 512);

        /**
         * Shipping Address
         */
        $redirectFields['ShippingAddress']['FirstName'] = substr($shippingAddress->getFirstname(), 0, 50);
        $redirectFields['ShippingAddress']['LastName'] = substr($shippingAddress->getLastname(), 0, 50);
        $redirectFields['ShippingAddress']['CompanyName'] = substr($shippingAddress->getCompany(), 0, 50);
        //$redirectFields['Shipping']['JobDescription'] = substr('', 0, 50);
        $redirectFields['ShippingAddress']['Street1'] = substr($shippingAddress->getStreet(1), 0, 50);
        if ($shippingAddress->getStreet(2)) {
            $redirectFields['ShippingAddress']['Street2'] = substr($shippingAddress->getStreet(2), 0, 50);
        }
        $redirectFields['ShippingAddress']['City'] = substr($shippingAddress->getCity(), 0, 50);
        $redirectFields['ShippingAddress']['State'] = substr($shippingAddress->getRegion(), 0, 50);
        $redirectFields['ShippingAddress']['PostalCode'] = substr($shippingAddress->getPostcode(), 0, 30);
        $redirectFields['ShippingAddress']['Country'] = strtolower($shippingAddress->getCountry());
        $email = $shippingAddress->getEmail();
        if (strlen($email <= 50)) {
            $redirectFields['ShippingAddress']['Email'] = $email;
        }
        if ($shippingAddress->getTelephone()) {
            //$redirectFields['ShippingAddress']['Phone'] = substr(preg_replace('/[^+()0-9]/', '', $shippingAddress->getTelephone()), 0, 32);
        }
        if ($shippingAddress->getFax()) {
            //$redirectFields['ShippingAddress']['Fax'] = substr(preg_replace('/[^+()0-9]/', '', $shippingAddress->getFax()), 0, 32);
        }

        /**
         * Shipping Method
         */
        $redirectFields['ShippingMethod'] = substr(Appmerce_Eway_Model_Config::SHIPPING_METHOD_UNKNOWN, 0, 30);

        /**
         * Line items
         */
        foreach ($order->getAllItems() as $orderItem) {
            if ($orderItem->getParentItemId()) {
                continue;
            }
            $redirectFields['Items']['SKU'] = substr($orderItem->getItemId(), 0, 12);
            $redirectFields['Items']['Description'] = substr($orderItem->getName(), 0, 26);
            $redirectFields['Items']['Quantity'] = substr(intval($orderItem->getQtyOrdered() + 0.5), 0, 6);
            $redirectFields['Items']['UnitCost'] = substr(intval(($orderItem->getBasePriceInclTax() * 100) + 0.5), 0, 8);
        }

        /**
         * Options
         */
        //$redirectFields['Options']['Value1'] = 'Option1';
        //$redirectFields['Options']['Value2'] = 'Option2';
        //$redirectFields['Options']['Value3'] = 'Option3';

        /**
         * Payment
         */
        $redirectFields['Payment']['TotalAmount'] = substr(intval(($order->getBaseGrandTotal() * 100) + 0.5), 0, 10);
        $redirectFields['Payment']['InvoiceNumber'] = substr($order->getIncrementId(), 0, 16);
        $redirectFields['Payment']['InvoiceDescription'] = substr($this->getConfig()->getOrderDescription($order), 0, 64);
        $redirectFields['Payment']['InvoiceReference'] = substr($order->getId(), 0, 50);
        $redirectFields['Payment']['CurrencyCode'] = substr($this->getCurrencyCode(), 0, 3);

        /**
         * Other
         */
        $redirectFields['CustomerIP'] = substr(Mage::helper('eway')->getRealIpAddr(), 0, 50);
        $redirectFields['Method'] = Appmerce_Eway_Model_Config::METHOD_PROCESS_PAYMENT;
        $redirectFields['TransactionType'] = Appmerce_Eway_Model_Config::TRANSACTION_TYPE_PURCHASE;
        //$redirectFields['DeviceID'] = substr('', 0, 50);
        //$redirectFields['PartnerID'] = substr('', 0, 50);
        $redirectFields['RedirectUrl'] = substr($this->getConfig()->getApiUrl('redirect', $storeId, true), 0, 512);
        $redirectFields['CancelUrl'] = substr($this->getConfig()->getApiUrl('cancel', $storeId, true), 0, 512);
        if ($this->getConfigData('logo_url', $storeId)) {
            $redirectFields['LogoUrl'] = substr($this->getConfigData('logo_url', $storeId), 0, 512);
        }
        if ($this->getConfigData('header_text', $storeId)) {
            $redirectFields['HeaderText'] = substr($this->getConfigData('header_text', $storeId), 0, 255);
        }
        $redirectFields['Language'] = substr($this->getConfigData('interface_language', $storeId), 0, 5);
        $redirectFields['CustomerReadOnly'] = true;
        //$redirectFields['CustomView'] = true;
        $redirectFields['VerifyCustomerPhone'] = false;
        $redirectFields['VerifyCustomerEmail'] = true;

        return $redirectFields;
    }

}
