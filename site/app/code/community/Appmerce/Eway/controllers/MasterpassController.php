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

class Appmerce_Eway_MasterpassController extends Appmerce_Eway_Controller_Common
{
    /**
     * Return payment API model
     *
     * @return Appmerce_Eway_Model_Api_Masterpass
     */
    protected function getApi()
    {
        return Mage::getSingleton('eway/api_masterpass');
    }

    /**
     * Render placement form
     *
     * @see eway/masterpass/placement
     */
    public function placementAction()
    {
        $this->saveCheckoutSession();
        $order = $this->getLastRealOrder();
        if ($order->getId()) {

            // Request
            $fields = $this->getApi()->getRedirectFields($order);
            try {
                $response = $this->getApi()->curlPostJson('CreateAccessCode', $fields, $order->getStoreId());
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

            // Save additional info into session
            $additionalInfo = array(
                'AccessCode' => $response->AccessCode,
                'FormActionURL' => $response->FormActionURL
            );
            Mage::getSingleton('checkout/session')->setData('additionalInfo', $additionalInfo);

            // Load layout
            $this->saveCheckoutSession();

            $this->loadLayout();
            $this->renderLayout();
        }
        else {
            $this->_redirect('checkout/cart', array('_secure' => true));
        }
    }

}
