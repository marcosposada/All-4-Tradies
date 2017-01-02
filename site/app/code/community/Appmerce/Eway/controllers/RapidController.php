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

class Appmerce_Eway_RapidController extends Appmerce_Eway_Controller_Common
{
    /**
     * Return payment API model
     *
     * @return Appmerce_Eway_Model_Api_Rapid
     */
    protected function getApi()
    {
        return Mage::getSingleton('eway/api_rapid');
    }

    /**
     * Confirm by params (frontend, adminhtml) redirect?AccessCode=
     */
    public function redirectAction()
    {
        $d3SecureUrl = $this->getRequest()->getParam('D3SecureUrl');

        if ($d3SecureUrl && strlen($d3SecureUrl) > 1) {
            $this->loadLayout();
            $this->renderLayout();
            return;
        }

        $accessCode = $this->getRequest()->getParam('AccessCode');
        $result = $this->confirm($accessCode);

        if ($result) {
            $this->getResponse()->setRedirect(Mage::getUrl('checkout/onepage/success', array(
                '_nosid' => true,
                '_secure' => true
            )));
        }
        else {
            $this->getResponse()->setRedirect(Mage::getUrl('checkout/onepage/failure', array(
                '_nosid' => true,
                '_secure' => true
            )));
        }
    }

    /**
     * Confirm by current session - ajax
     */
    public function confirmAction()
    {
        $accessCode = Mage::helper('eway')->getOrderAccessCode();

        $result = $this->confirm($accessCode);

        $jsonBlock = array();
        if ($result) {
            $jsonBlock['success'] = true;
            $jsonBlock['error'] = false;
            $jsonBlock['redirect'] = Mage::getUrl('checkout/onepage/success', array(
                '_nosid' => true,
                '_secure' => true
            ));
            $jsonBlock['error_messages'] = $this->__('');
        }
        else {
            $jsonBlock['success'] = false;
            $jsonBlock['error'] = true;
            $jsonBlock['redirect'] = Mage::getUrl('checkout/onepage/failure', array(
                '_nosid' => true,
                '_secure' => true
            ));
            $jsonBlock['error_messages'] = $this->__('Error!');
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($jsonBlock));
    }

    /**
     * Confirm from adminhtml
     */
    public function adminAction()
    {
        $accessCode = $this->getRequest()->getParam('AccessCode');
        $result = $this->confirm($accessCode);
        $this->getResponse()->setBody('');
    }

    /**
     * Confirm eway transaction
     */
    private function confirm($accessCode)
    {
        /* request from adminhtml */
        $accessCodeAdmin = $this->getRequest()->getParam('accesscode');
        $accessCode = $accessCodeAdmin ? $accessCodeAdmin : Mage::helper('eway')->getOrderAccessCode();

        if ($accessCode === null) {
            return false;
        }

        // JSON request
        try {
            $response = $this->getApi()->curlPostJson('GetAccessCodeResult', array('AccessCode' => $accessCode), false);
        }
        catch (Exception $e) {
            Mage::throwException($e->getMessage());
        }

        // Debug
        if ($this->getApi()->getConfigData('debug_flag')) {
            Mage::getModel('eway/api_debug')->setDir('out')->setUrl('checkout/onepage')->setInfo($accessCode)->save();
            Mage::getModel('eway/api_debug')->setDir('in')->setUrl('checkout/onepage')->setInfo($response)->save();
        }

        // Process the final access response!
        $gatewayTransactionId = $response->TransactionID;

        // Build response note for backend
        $fraud = 0;

        /* last order */
        $order = Mage::helper('eway')->getOrder();
        $payment = $order->getPayment();

        /* additional info from eway */
        $additionalInfo = Mage::helper('eway')->stdToArray($response, 'AccessCodeResult');
        $note = Mage::helper('eway')->__('Response: %s (%s).', $response->ResponseMessage, $response->ResponseCode);
        $additionalInfo['note'] = $note;

        // Switch response
        $result = false;
        switch ($response->TransactionStatus) {
            case 1 :
                $this->getProcess()->success($order, $note, $gatewayTransactionId, 1, false);
                $result = true;
                break;

            default :
                $this->_addTransaction($payment, 0, Mage_Sales_Model_Order_Payment_Transaction::TYPE_PAYMENT, array('is_transaction_closed' => 0), $additionalInfo, $note);
                $order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true);
        }
        $order->save();
        return $result;
    }

    /**
     * get Eway JS Block for current session
     *
     * @return string
     */
    public function ewayjsAction()
    {
        $this->loadLayout('eway_rapid_checkout_onepage_review');
        $html = $this->getLayout()->getBlock('appmerce_eway_review_rapid_save')->toHtml();
        $this->getResponse()->setBody($html);
    }

    /**
     * Merge payment transaction
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @param string $transactionId
     * @param string $transactionType
     * @param array $transactionDetails
     * @param array $transactionAdditionalInfo
     * @return null|Mage_Sales_Model_Order_Payment_Transaction
     */
    private function _addTransaction(Mage_Sales_Model_Order_Payment $payment, $transactionId, $transactionType, array $transactionDetails = array(), array $transactionAdditionalInfo = array(), $message = false)
    {
        $message = $message . '<br />';

        $newIsResultTr = ($transactionId == 'AccessCode');
        if ($newIsResultTr) {
            $transactionId = $transactionId . '-' . Mage::helper('eway')->getTransactionCount($payment);
        }
        $payment->setTransactionId($transactionId);

        $transaction = $payment->addTransaction($transactionType, null, false, $message);

        /* set transaction additional info */
        $transaction->setAdditionalInformation(Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS, $transactionAdditionalInfo);
        $payment->unsLastTransId();

        /**
         * It for self using
         */
        $transaction->setMessage($message);

        $transaction->save();
        $payment->save();

        return $transaction;
    }

    /**
     * Blank page
     */
    public function blankAction()
    {
    }

}
