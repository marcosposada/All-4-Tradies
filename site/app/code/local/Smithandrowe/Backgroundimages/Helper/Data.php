<?php

    class Smithandrowe_Backgroundimages_Helper_Data extends Mage_Core_Helper_Abstract
    {

        public function getPromo()
        {
            // Define directory for store view
            //$store = Mage::app()->getStore()->getStoreId();
            $store = Mage::getModel('core/store')->load(Mage::app()->getStore()->getStoreId());
            $dir = $store->getCode();
            $media = Mage::getBaseDir('media');
            // Detect if view has background
            // Modules do not have a background, so they need to be defined here.
            $bg = false;
            $background = false;
                $img = Mage::getBlockSingleton('cms/page')->getPage()->getPromo();
                $path = '/wysiwyg/'.$dir.'/blocks/cms/promo/';
                if ($img) {
                    $bg = true;
                    $background_dir = $media.$path;
                    $background['img'] = '/media/'.$img;
                } else {
                    $background_dir = $media.$path;
                    $background['img'] = Mage::helper('backgroundimages/data')->randomImage($background_dir);
                    switch ($background['img']) {
                        case "A4T-PromoBlocks-140903_V12.png":
                            $background['link'] = 'google.com';
                            break;
                        case "A4T-PromoBlocks-140903_V1.png":
                            $background['link'] = '/quickrfq';
                            break;
                        case "A4T-PromoBlocks-140903_V12.png":
                            $background['link'] = '/forms/feedback';
                            break;
                        case "A4T-PromoBlocks-140903_V14.png":
                            $background['link'] = '/customer/account/create';
                            break;
                        case "A4T-PromoBlocks-140903_V15.png":
                            $background['link'] = '/customer/account/create';
                            break;
                        case "A4T-PromoBlocks-140903_V18.png":
                            $background['link'] = 'http://facebook.com/all4tradies';
                            break;
                        case "A4T-PromoBlocks-140903_V19.png":
                            $background['link'] = 'http://twitter.com/all4tradies';
                            break;
                        case "A4T-PromoBlocks-140903_V19.png":
                            $background['link'] = 'http://www.linkedin.com/company/3189301';
                            break;
                        case "A4T-PromoBlocks-140903_V111.png":
                            $background['link'] = 'http://plus.google.com/+All4tradiesAu/.com';
                            break;
                    }
                    $background['img'] = Mage::getBaseUrl('media').$path.$background['img'];
                }
            return $background;
        }

        public function getBackground()
        {

            // Define directory for store view
            //$store = Mage::app()->getStore()->getStoreId();
            $store = Mage::getModel('core/store')->load(Mage::app()->getStore()->getStoreId());
            $dir = $store->getCode();
            $media = Mage::getBaseDir('media');
            // Detect if view has background
            // Modules do not have a background, so they need to be defined here.
            $bg = false;
                $img = Mage::getBlockSingleton('cms/page')->getPage()->getBackground();
                $path = '/wysiwyg/'.$dir.'/blocks/cms/background/';
                if ($img) {
                    $bg = true;
                    $background_dir = $media.$path;
                    $background['img'] = '/media/'.$img;
                } else {
                    $background_dir = $media.$path;
                    $background['img'] = Mage::helper('backgroundimages/data')->randomImage($background_dir);
                    $background['img'] = Mage::getBaseUrl('media').$path.$background['img'];
            }


            return $background;
        }

        public function randomImage($dir)
        {


            $file_list = NULL;
            $direc = (is_dir($dir)) ? $direc = $dir : $direc = dirname($dir);
            if (isset($direc)) :
                $dh = opendir($direc);
            else :
                return false;
            endif;

            while ($file = readdir($dh)) {
                if (is_file("${direc}/${file}"))
                    if ($file != ".DS_Store")
                        $file_list[] = $file;
            }

            closedir($dh);
            srand((double)microtime() * 100000);
            /* pick something from the file list at random */

            return (sizeof($file_list) > 0) ? $file_list[rand(0, (sizeof($file_list) - 1))] : false;
        }

    }
