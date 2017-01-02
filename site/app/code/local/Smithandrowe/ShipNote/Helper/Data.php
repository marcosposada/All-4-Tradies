<?php
    class Smithandrowe_ShipNote_Helper_Data extends Mage_Core_Helper_Abstract
    {
        const STORE_CONFIG_PATH_ENABLED        = 'shipnote_options/basic_settings/enabled';
        const STORE_CONFIG_PATH_FRONTEND_LABEL = 'shipnote_options/basic_settings/frontend_label';

        /**
         * @return bool
         */
        public function isEnabled()
        {
            Mage::log('test');
            if (false === parent::isModuleEnabled()) {
                return false;
            }
            return Mage::getStoreConfigFlag(self::STORE_CONFIG_PATH_ENABLED);
        }

        /**
         * @return string
         */
        public function getFrontendLabel()
        {
            return Mage::getStoreConfig(self::STORE_CONFIG_PATH_FRONTEND_LABEL);
        }
    }
