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

class Appmerce_Eway_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Get Real IP Address
     *
     * @return string
     */
    public function getRealIpAddr()
    {
        $ip = '';
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     * Get last order for current session
     *
     * @return Mage_Sales_Model_Order|false
     */
    public function getOrder()
    {
        $order_id = Mage::getSingleton('checkout/session')->getLastRealOrderId();
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($order_id);
        return $order;
    }

    /**
     * Get FormActionURL
     *
     * @param Mage_Checkout_Model_Order $order
     * @return string|null
     */
    public function getFormActionURL($order = null)
    {
        if (!$order) {
            $order = $this->getOrder();
        }
        if ($order && $order->getId()) {
            $url = $order->getPayment()->getAdditionalInformation('eway_form_action_url');
            return $url;
        }
        return null;
    }

    /**
     * Get AccessCods code
     *
     * @param Mage_Checkout_Model_Order $order
     * @return string|null
     */
    public function getOrderAccessCode($order = null)
    {
        if (!$order) {
            $order = $this->getOrder();
        }

        if ($order && $order->getId()) {
            $accessCode = $order->getPayment()->getAdditionalInformation('eway_access_code');
            return $accessCode;
        }
        return null;
    }

    /* tools for additional transaction info */
    public function stdToArray($obj, $pref = null)
    {
        $pref = $pref ? $pref . '-' : '';
        $ret = array();
        $rc = (array)$obj;
        foreach ($rc as $key => $field) {
            if (!(is_array($field) || is_object($field)))
                $ret[$pref . $key] = $field;
        }
        return $ret;
    }

    /**
     * Find one transaction by ID
     *
     * @param Mage_Sales_Model_Order_Payment
     * @param string $txnId
     * @return Mage_Sales_Model_Order_Payment_Transaction|false
     */
    public function lookUpTransaction($payment, $txnId)
    {
        $txn = Mage::getModel('sales/order_payment_transaction')->setOrderPaymentObject($payment)->loadByTxnId($txnId);
        return $txn;
    }

    /**
     * Get transaction count
     *
     * @param Mage_Sales_Model_Order_Payment
     * @return Mage_Sales_Model_Order_Payment_Transaction|false
     */
    public function getTransactionCount($payment)
    {
        $collection = Mage::getModel('sales/order_payment_transaction')->getCollection()->addPaymentIdFilter($payment->getId());
        return $collection->count();
    }

    /**
     * Get Magento version
     *
     * @return float
     */
    public function getMagentoVersion()
    {
        return (float)substr(Mage::getVersion(), 0, 3);
    }

}
