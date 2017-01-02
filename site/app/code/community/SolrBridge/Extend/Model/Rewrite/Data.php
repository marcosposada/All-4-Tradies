<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade MongoBridge to newer
 * versions in the future.
 *
 * @category    SolrBridge
 * @package     SolrBridge_Extend
 * @author      Hau Danh
 * @copyright   Copyright (c) 2011-2014 Solr Bridge (http://www.solrbridge.com)
 */
class SolrBridge_Extend_Model_Rewrite_Data extends SolrBridge_Solrsearch_Model_Data
{
	public function prepareFinalProductData($_product, &$docData)
	{
		$store = $this->store;
		//Remove store from Product Url
		$remove_store_from_url = Mage::helper('solrsearch')->getSetting('remove_store_from_url');

		if (intval($remove_store_from_url) > 0) {
			$params['_store_to_url'] = false;
			$productUrl = $_product->getUrlInStore($product, $params);
			$baseurl = $store->getBaseUrl();
			$productUrl = str_replace($baseurl, '/', $productUrl);
		}else{
			$productUrl = $_product->getProductUrl();
		}

		if (strpos($productUrl, 'solrbridge.php')) {
			$productUrl = str_replace('solrbridge.php', 'index.php', $productUrl);
		}

		$sku = $_product->getSku();
		$this->pushTextSearch ( $sku );
		$this->pushTextSearch ( str_replace(array('-', '_'), '', $sku) );

		$docData['url_path_varchar'] = $productUrl;
		$productName = $_product->getName();
		$docData['name_varchar'] = $productName;
		$docData['name_boost'] = $productName;
		$docData['name_boost_exact'] = $productName;
		$docData['name_relative_boost'] = $productName;

		$docData['attribute_set_varchar'] = Mage::getModel('eav/entity_attribute_set')->load($_product->getAttributeSetId())->getAttributeSetName();
		$this->pushTextSearch ( $docData['attribute_set_varchar'] );

		$this->pushTextSearch ( $productName );

		$catIndexPosition = $_product->getData('cat_index_position');

		if (!empty($catIndexPosition) && is_numeric($catIndexPosition)) {
			$docData['sort_position_decimal'] = floatval($catIndexPosition);
		}else{
			$docData['sort_position_decimal'] = 0;
		}

		$docData['sort_bestselling_decimal'] = $this->getProductOrderedQty($_product, $this->store);


		$docData['products_id'] = $_product->getId();
		$docData['product_type_static'] = (string)$_product->getTypeId();
		$docData['unique_id'] = $store->getId().'P'.$_product->getId();
		if (!isset($docData['product_search_weight_int'])) {
			$docData['product_search_weight_int'] = 0;
		}

		$multipleStoreModeSetting = Mage::helper('solrsearch')->getSetting('multiplestore');
		if (intval($multipleStoreModeSetting) > 0) {//multiple store by different category root and different website
		    $docData['store_id'] = $store->getId();
		    $docData['website_id'] = $store->getWebsiteId();
		}else{
		    if(isset($docData['category_id']) && !empty($docData['category_id'])){
		        $docData['store_id'] = $store->getId();
		        $docData['website_id'] = $store->getWebsiteId();
		    }else{
		        $docData['store_id'] = 0;
		        $docData['website_id'] = 0;
		    }
		}

		$docData['filter_visibility_int'] = $_product->getVisibility();
		
		$docData['DOCTYPE_static'] = 'PRODUCT';

		$checkInstockMethod =  Mage::helper('solrsearch')->getSetting('check_instock_method');

		try
		{
    		$stock = Mage::getModel ( 'cataloginventory/stock_item' )->loadByProduct ( $_product );

    		if (intval($checkInstockMethod) > 0)
    		{
    		    if ($stock->getIsInStock() && $stock->getQty() > 0)
    		    {
    		        $docData['instock_int'] = 1;
    		    }
    		    else
    		    {
    		        $docData['instock_int'] = 0;
    		    }
    		}
    		else
    		{
    		    if ($stock->getIsInStock())
    		    {
    		        $docData['instock_int'] = 1;
    		    }
    		    else
    		    {
    		        $docData['instock_int'] = 0;
    		    }
    		}

		}
		catch (Exception $e)
		{
            $docData['instock_int'] = 0;
    	}
		$docData['product_status'] = $_product->getStatus();
	}
}