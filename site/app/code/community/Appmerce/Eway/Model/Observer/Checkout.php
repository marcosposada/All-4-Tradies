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

class Appmerce_Eway_Model_Observer_Checkout
{
    /**
     * Save order into registry to use it in the overloaded controller.
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Paypal_Model_Observer
     */
    public function saveOrderAfterSubmit(Varien_Event_Observer $observer)
    {
        $order = $observer->getEvent()->getData('order');
        Mage::register('eway_rapid_order', $order, true);

        /* for magento bug start*/
        if ($order && $order->getId()) {
            $payment = $order->getPayment();
            $transaction = Mage::helper('eway')->lookUpTransaction($payment, $payment->getLastTransId());
            $additionalInfo = Mage::getSingleton('checkout/session')->getData('additionalInfo');
            $additionalInfo = $additionalInfo ? $additionalInfo : array();
            $transaction->setAdditionalInformation(Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS, $additionalInfo);
            $transaction->save();
            /* for magento bug end*/
        }

        return $this;
    }

    /**
     * Set data for response of frontend saveOrder action
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Paypal_Model_Observer
     */
    public function setResponseAfterSaveOrder(Varien_Event_Observer $observer)
    {
        $order = Mage::registry('eway_rapid_order');

        if ($order && $order->getId()) {
            $payment = $order->getPayment();
            if ($payment && $payment->getMethod() === Appmerce_Eway_Model_Config::METHOD_RAPID) {

                $controller = $observer->getEvent()->getData('controller_action');
                $controller->loadLayout('eway_rapid_checkout_onepage_review');

                $html = $controller->getLayout()->getBlock('appmerce_eway_review_rapid_save')->toHtml();
                $controller->getResponse()->clearHeader('Location');

                $result['update_section'] = array(
                    'name' => 'rapid-eway',
                    'html' => $html
                );
                $result['redirect'] = false;
                $result['success'] = false;
                $controller->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));

            }
        }

        return $this;
    }

}
