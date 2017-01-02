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
 * @license     http://opensource.org/licenses/osl-3.0.	php  Open Software License (OSL 3.0)
 */

class Appmerce_Eway_ApiController extends Appmerce_Eway_Controller_Common
{
    /**
     * Return payment API model
     *
     * @return Appmerce_Eway_Model_Api_Redirect
     */
    protected function getApi()
    {
        return Mage::getSingleton('eway/api_redirect');
    }

    /**
     * Render placement form
     *
     * @see eway/api/placement
     */
    public function placementAction()
    {
        $this->saveCheckoutSession();
        $order = $this->getLastRealOrder();
        if ($order->getId()) {

            // Request
            $fields = $this->getApi()->getRedirectFields($order);
            try {
                $response = $this->getApi()->curlPostJson('CreateAccessCodeShared', $fields, $order->getStoreId());
            }
            catch (Exception $e) {
                $this->getProcess()->cancel($order, Mage::helper('eway')->__('Error: check the API Key and Password configuration.'), 0, 1, true);
                $this->_redirect('checkout/cart', array('_secure' => true));
                return;
            }

            // Debug
            if ($this->getApi()->getConfigData('debug_flag')) {
                $url = $this->getRequest()->getPathInfo();
                Mage::getModel('eway/api_debug')->setDir('out')->setUrl($url)->setInfo($fields)->save();
                Mage::getModel('eway/api_debug')->setDir('in')->setUrl($url)->setInfo($response)->save();
            }

            // Check for errors
            if (isset($response->Errors) && !empty($response->Errors)) {
                $note = Mage::helper('eway')->__('Errors: %s.', $response->Errors);
                $note .= '<br />' . Mage::helper('eway')->__('Documentation: %s.', 'http://www.apihub.com/eway/api/eway-rapid-31-api/docs/responses');
                $this->getProcess()->cancel($order, $note, 'Process', 1, true);
                $this->_redirect('checkout/cart', array('_secure' => true));
                return;
            }

            // Redirect user
            $this->_redirectUrl($response->SharedPaymentUrl);
        }
        else {
            $this->_redirect('checkout/cart', array('_secure' => true));
        }
    }

    /**
     * Redirect action
     *
     * @see eway/api/redirect
     */
    public function redirectAction()
    {
        $params = $this->getRequest()->getParams();
        if (isset($params['AccessCode'])) {
            $response = $this->getApi()->curlPostJson('GetAccessCodeResult', $params, Mage::app()->getStore()->getStoreId());

            // Debug
            if ($this->getApi()->getConfigData('debug_flag')) {
                $url = $this->getRequest()->getPathInfo();
                Mage::getModel('eway/api_debug')->setDir('out')->setUrl($url)->setInfo($params)->save();
                Mage::getModel('eway/api_debug')->setDir('in')->setUrl($url)->setInfo($response)->save();
            }

            $order = Mage::getModel('sales/order')->load($response->InvoiceReference);
            $note = Mage::helper('eway')->__('Response: %s (%s).', $response->ResponseMessage, $response->ResponseCode);
            $transactionId = $response->TransactionID;

            // Switch response by status or code
            if (isset($response->TransactionStatus)) {
                switch ($response->TransactionStatus) {
                    case '1' :
                        $this->getProcess()->success($order, $note, $transactionId, 1, true);
                        $this->_redirect('checkout/onepage/success', array('_secure' => true));
                        break;

                    default :
                        $this->getProcess()->cancel($order, $note, $transactionId, 1, true);
                        $this->_redirect('checkout/cart', array('_secure' => true));
                }
            }
            else {
                switch ($response->ResponseCode) {
                    case '00' :
                    case '08' :
                    case '10' :
                    case '11' :
                    case '16' :
                        $this->getProcess()->success($order, $note, $transactionId, 1, true);
                        $this->_redirect('checkout/onepage/success', array('_secure' => true));
                        break;

                    default :
                        $this->getProcess()->cancel($order, $note, $transactionId, 1, true);
                        $this->_redirect('checkout/cart', array('_secure' => true));
                }
            }
        }
        else {
            $this->getProcess()->repeat();
            $this->_redirect('checkout/cart', array('_secure' => true));
        }
    }

    /**
     * Cancel action
     *
     * @see eway/api/cancel
     */
    public function cancelAction()
    {
        $this->getProcess()->repeat();
        $this->_redirect('checkout/cart', array('_secure' => true));
    }

}
