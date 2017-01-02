<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade MongoBridge to newer
 * versions in the future.
 *
 * @category    SolrBridge
 * @package     SolrBridge_Solrsearch
 * @author      Hau Danh
 * @copyright   Copyright (c) 2011-2014 Solr Bridge (http://www.solrbridge.com)
 */
class SolrBridge_Extend_Model_Observer
{
	public function generateStaticConfig()
	{
		$advanced_autocomplete = (int)Mage::helper('solrsearch')->getSetting('advanced_autocomplete');
		if ($advanced_autocomplete > 0)
		{
			$sbsextconfig = Mage::getStoreConfig('sbsext/settings', 0);
	
			$etcDir = '/'.trim(Mage::getBaseDir('etc'), '/');
			
			$configData = file_get_contents( $etcDir.'/solrbridge.conf' );
			$config = json_decode($configData, true);

			$config['settings'] = array_merge($config['settings'], $sbsextconfig);
	
			$stores = Mage::app()->getStores();
			foreach ($stores as $store)
			{
				$config['stores'][$store->getId()]['settings'] = array_merge($config['stores'][$store->getId()]['settings'], Mage::getStoreConfig('sbsext/settings', $store->getId()));
			}
	
			$configFile = fopen($etcDir.'/solrbridge.conf', 'w');
			fwrite($configFile, json_encode($config));
			fclose($configFile);
		}
	}
}