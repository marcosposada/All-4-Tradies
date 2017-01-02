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

class Appmerce_Eway_Model_Api_Debug extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('eway/api_debug');
    }

    /**
     * Set debug information
     *
     * @param mixed $data
     */
    public function setInfo($data)
    {
        $modify = is_object($data) ? (array)$data : $data;

        if (is_array($modify)) {
            if (array_key_exists('ewayCardNumber', $modify)) {
                $modify['ewayCardNumber'] = preg_replace('/(\d{4})(\d{2})(\d{2})(\d{4})(\d{4})/', '$1 $2## #### $4', $modify['ewayCardNumber']);
            }
            if (array_key_exists('ewayCardExpiryMonth', $modify)) {
                $modify['ewayCardExpiryMonth'] = '##';
            }
            if (array_key_exists('ewayCardExpiryYear', $modify)) {
                $modify['ewayCardExpiryYear'] = '##';
            }
            if (array_key_exists('ewayCVN', $modify)) {
                $modify['ewayCVN'] = '###';
            }

            $modify = print_r($modify, true);
        }
        $this->setData('data', $modify);
        return $this;
    }

}
