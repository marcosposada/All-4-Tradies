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
class SolrBridge_Extend_Model_Utility extends SolrBridge_Solrsearch_Model_Ultility
{
	
	public function getCmsPageCollectionByStoreId( $storeId )
	{
		$oldStore = Mage::app ()->getStore ();
		Mage::app ()->setCurrentStore ( $storeId );
		
		$collection = Mage::getModel('cms/page')->getCollection();//->addStoreFilter( $storeId );
		
		Mage::app ()->setCurrentStore ( $oldStore );
		return $collection;
	}
	
	public function getCMSPageCollectionMetaData( $solrcore )
	{
		$storeIds = $this->getMappedStoreIdsFromSolrCore( $solrcore );
		$metaDataArray = array();
		if ( is_array($storeIds) )
		{
			$itemsPerCommit = $this->itemsPerCommit;
			$totalCmsPageCount = 0;
		
			$loadedStores = array();
			$loadedStoresName = array();
		
			foreach ($storeIds as $storeId) {
				$storeCmsPageCollection = $this->getCmsPageCollectionByStoreId( $storeId );
				$storeCmsPageCount = $storeCmsPageCollection->getSize();
				$totalCmsPageCount += $storeCmsPageCount;
				$metaDataArray['stores'][$storeId]['cmspageCount'] = $totalCmsPageCount;
		
				$totalPages = ceil($storeCmsPageCount/$itemsPerCommit);
				$metaDataArray['stores'][$storeId]['totalPages'] = $totalPages;
				$metaDataArray['stores'][$storeId]['collection'] = $storeCmsPageCollection;
		
				$store = Mage::getModel('core/store')->load($storeId);
				$loadedStores[$storeId] = $store;
				$loadedStoresName[$storeId] = $store->getName();
			}
			$metaDataArray['totalCmsPageCount'] = $totalCmsPageCount;
			$metaDataArray['loadedStores'] = $loadedStores;
			$metaDataArray['loadedStoresName'] = $loadedStoresName;
		}
		return $metaDataArray;
	}
	
	
	public function getBlogCollectionByStoreId( $storeId )
	{
		$oldStore = Mage::app ()->getStore ();
		Mage::app ()->setCurrentStore ( $storeId );
	
		$collection = Mage::getModel('neotheme_blog/post')->getCollection();//->addStoreFilter( $storeId );
	
		Mage::app ()->setCurrentStore ( $oldStore );
		return $collection;
	}
	
	public function getBlogCollectionMetaData( $solrcore )
	{
		$storeIds = $this->getMappedStoreIdsFromSolrCore( $solrcore );
		$metaDataArray = array();
		if ( is_array($storeIds) )
		{
			$itemsPerCommit = $this->itemsPerCommit;
			$totalBlogCount = 0;
		
			$loadedStores = array();
			$loadedStoresName = array();
		
			foreach ($storeIds as $storeId) {
				$storeBlogCollection = $this->getBlogCollectionByStoreId( $storeId );
				$storeBlogCount = $storeBlogCollection->getSize();
				$totalBlogCount += $storeBlogCount;
				$metaDataArray['stores'][$storeId]['blogCount'] = $totalBlogCount;
		
				$totalPages = ceil($storeBlogCount/$itemsPerCommit);
				$metaDataArray['stores'][$storeId]['totalPages'] = $totalPages;
				$metaDataArray['stores'][$storeId]['collection'] = $storeBlogCollection;
		
				$store = Mage::getModel('core/store')->load($storeId);
				$loadedStores[$storeId] = $store;
				$loadedStoresName[$storeId] = $store->getName();
			}
			$metaDataArray['totalBlogCount'] = $totalBlogCount;
			$metaDataArray['loadedStores'] = $loadedStores;
			$metaDataArray['loadedStoresName'] = $loadedStoresName;
		}
		return $metaDataArray;
	}
	
	public function parseCmsPageJsonData($collection, $store)
	{
		$fetchedCmsPage = 0;
		
		$index = 1;
		$documents = "{";
		
		foreach ($collection as $_cmspage)
		{
			$textSearch = array();
			$textSearchText = array();
			$docData = array();
			
			$textSearch[] = $_cmspage->getData('title');
			$textSearch[] = $_cmspage->getData('meta_keywords');
			$textSearch[] = $_cmspage->getData('meta_description');
			
			$cmsPageTitle = $_cmspage->getData('title');
			
			$docData['products_id'] = $_cmspage->getData('page_id');
			$docData['DOCTYPE_static'] = 'CMSPAGE';
			$docData['unique_id'] = $store->getId().'CMS'.$_cmspage->getData('page_id');
			
			$docData['category_id'] = array();
			$docData['store_id'] = $store->getId();
			$docData['website_id'] = $store->getWebsiteId();
			$docData['category_path'] = array();
			$docData['sku'] = 'cmspage-'.$_cmspage->getData('identifier');
			
			$docData['name_varchar'] = $cmsPageTitle;
			$docData['name_boost'] = $cmsPageTitle;
			$docData['name_boost_exact'] = $cmsPageTitle;
			$docData['name_relative_boost'] = $cmsPageTitle;
			
			$docData['url_path_varchar'] = Mage::getUrl(null, array('_direct' => $_cmspage->getIdentifier()));
			
			$textSearchText[] = Mage::getSingleton('widget/template_filter')->filter($_cmspage->getData('content'));

			
			//Prepare text search data
			$docData['textSearch'] = (array) $textSearch;
			$docData['textSearchText'] = (array) $textSearchText;
			$docData['textSearchStandard'] = (array) $textSearch;
			$docData['textSearchGeneral'] = (array) $textSearch;
			
			$documents .= '"add": '.json_encode(array('doc'=>$docData)).",";
			$index++;
			$fetchedCmsPage++;
			unset( $_cmspage );
		}
		
		$jsonData = trim($documents,",").'}';
		
		return array('jsondata'=> $jsonData, 'fetchedCmsPages' => (int) $fetchedCmsPage);
	}
	
	public function parseBlogJsonData($collection, $store)
	{
		$fetchedBlog = 0;
	
		$index = 1;
		$documents = "{";
	
		foreach ($collection as $_blog)
		{
			$textSearch = array();
			$textSearchText = array();
			$docData = array();
				
			$textSearch[] = $_blog->getData('title');
			$textSearch[] = $_blog->getData('meta_keywords');
			$textSearch[] = $_blog->getData('meta_description');
			$textSearch[] = $_blog->getData('meta_title');
				
			$blogTitle = $_blog->getData('title');
				
			$docData['products_id'] = $_blog->getId();
			$docData['DOCTYPE_static'] = 'NEOBLOG';
			$docData['unique_id'] = $store->getId().'NEOBLOG'.$_blog->getId();
				
			$docData['category_id'] = array();
			$docData['store_id'] = $store->getId();
			$docData['website_id'] = $store->getWebsiteId();
			$docData['category_path'] = array();
			$docData['sku'] = 'neoblog-'.$_blog->getData('cms_identifier');
				
			$docData['name_varchar'] = $blogTitle;
			$docData['name_boost'] = $blogTitle;
			$docData['name_boost_exact'] = $blogTitle;
			$docData['name_relative_boost'] = $blogTitle;
				
			$docData['url_path_varchar'] = $_blog->getReadMoreUrl();
			//Mage::getUrl(null, array('_direct' => $_blog->getData('cms_identifier')));
				
			$textSearchText[] = Mage::getSingleton('widget/template_filter')->filter($_blog->getData('content_html'));
	
				
			//Prepare text search data
			$docData['textSearch'] = (array) $textSearch;
			$docData['textSearchText'] = (array) $textSearchText;
			$docData['textSearchStandard'] = (array) $textSearch;
			$docData['textSearchGeneral'] = (array) $textSearch;
				
			$documents .= '"add": '.json_encode(array('doc'=>$docData)).",";
			$index++;
			$fetchedBlog++;
			unset( $_blog );
		}
	
		$jsonData = trim($documents,",").'}';
	
		return array('jsondata'=> $jsonData, 'fetchedBlogs' => (int) $fetchedBlog);
	}
}