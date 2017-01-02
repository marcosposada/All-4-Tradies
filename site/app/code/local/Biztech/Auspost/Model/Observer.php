<?php
    class Biztech_Auspost_Model_Observer {
     

        public function checkKey($observer)
        {
            $isdefault = '';
            $params    = Mage::app()->getRequest()->getParam('groups');
            if(isset($params['activation']['fields']['key']['inherit'])){
                $isdefault = $params['activation']['fields']['key']['inherit'];
            }

            if($isdefault == 1){
                $key = Mage::getStoreConfig('auspost/activation/key');
            }else{
                $key = $params['activation']['fields']['key']['value'];
            }

            $website_code =  Mage::app()->getRequest()->getParam('website');
            if($website_code == null || $website_code == '')
            {
                $website_code =  Mage::app()->getWebsite()->getWebsiteId();
            }
            $website = Mage::getModel('core/website')->load($website_code);
            $domains = array();

            $url = $website->getConfig('web/unsecure/base_url');
            if($domain = trim(preg_replace('/^.*?\\/\\/(.*)?\\//', '$1', $url))){
                $domains[] = $domain;
            }
            $url = $website->getConfig('web/secure/base_url');
            if($domain = trim(preg_replace('/^.*?\\/\\/(.*)?\\//', '$1', $url))){
                $domains[] = $domain;
            }
            $dom =  array_unique($domains);
            Mage::helper('auspost')->checkKey($key,$dom);

        }

}