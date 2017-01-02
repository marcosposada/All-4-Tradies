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
class SolrBridge_Extend_Block_Adminhtml_Index_Management extends SolrBridge_Solrsearch_Block_Adminhtml_Solrsearch
{
	protected $utility = null;
	
	public function __construct()
	{
		$this->utility = Mage::getModel('sbsext/utility');
		$this->setTemplate('solrsearch/solrsearch.phtml');
	}
	
	public function getCmsPageIndexedPercent()
	{
		
	}
	
	public function getActiveSolrCores()
	{
		$availableCores = $this->utility->getAvailableCores();
	
		$activeSolrCores = array();
	
		foreach ($availableCores as $solrcore => $infoArray)
		{
			$storeIds = $this->utility->getMappedStoreIdsFromSolrCore($solrcore);
	
			if ( !empty($storeIds) )
			{
				$collectionMetaData = $this->utility->getProductCollectionMetaData($solrcore);
				$cmsPageCollectionMetaData = $this->utility->getCMSPageCollectionMetaData($solrcore);
	
				$storeLabels = array();
				$productCount = (int) $collectionMetaData['totalProductCount'];
				$websiteids = array();
	
				$loadedStores = $collectionMetaData['loadedStores'];
	
				foreach ($loadedStores as $storeid => $storeObject)
				{
					$storeLabels[] = $storeObject->getWebsite()->getName().'-'.$storeObject->getName().'(<b>'.$collectionMetaData['stores'][$storeid]['productCount'].'</b> products)';
					$websiteids[] = $storeObject->getWebsiteId();
				}
	
				$infoArray['productCount'] = $productCount;
	
				$infoArray['websiteids'] = $websiteids;
	
				$infoArray['labels'] = $storeLabels;
	
				$infoArray['solrluke'] = Mage::getResourceModel('solrsearch/solr')->getSolrLuke($solrcore);
				
				
				$infoArray['cmsPageCount'] = (int) $cmsPageCollectionMetaData['totalCmsPageCount'];;
				$infoArray['cmsPageDocumentCounts'] = Mage::helper('sbsext')->getTotalCmsPageDocumentsByCore($solrcore);
				
				$infoArray['blogCount'] = 0;
				$infoArray['blogDocumentCounts'] = 0;
				if ( Mage::getStoreConfig('sbsext/settings/allow_blog_search') > 0 && Mage::helper('core')->isModuleEnabled('NeoTheme_Blog') )
				{
					$blogCollectionMetaData = $this->utility->getBlogCollectionMetaData($solrcore);
					$infoArray['blogCount'] = (int) $blogCollectionMetaData['totalBlogCount'];
					$infoArray['blogDocumentCounts'] = Mage::helper('sbsext')->getTotalBlogDocumentsByCore($solrcore);
				}
	
				$activeSolrCores[$solrcore] = $infoArray;
			}
		}
	
		return $activeSolrCores;
	}
}