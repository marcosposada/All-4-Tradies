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
class SolrBridge_Extend_Model_Resource_Solr extends SolrBridge_Solrsearch_Model_Resource_Solr
{
	public function truncateCmsPageSolrDocuments( $solrcore )
	{
		$solrServerUrl =$this->getSolrServerUrl();

    	$storeMappingString = Mage::getStoreConfig('solrbridgeindices/'.$solrcore.'/stores', 0);

    	//Solr delete all docs from index
    	$clearnSolrIndexUrl = trim($solrServerUrl,'/').'/'.$solrcore.'/update?stream.body=<delete><query>DOCTYPE_static:CMSPAGE</query></delete>&commit=true';
    	
    	$this->doRequest($clearnSolrIndexUrl);

    	Mage::app()->getCache()->clean('matchingAnyTag', array('solrbridge_solrsearch'));
	}
	
	public function truncateBlogSolrDocuments( $solrcore )
	{
		$solrServerUrl =$this->getSolrServerUrl();
	
		$storeMappingString = Mage::getStoreConfig('solrbridgeindices/'.$solrcore.'/stores', 0);
	
		//Solr delete all docs from index
		$clearnSolrIndexUrl = trim($solrServerUrl,'/').'/'.$solrcore.'/update?stream.body=<delete><query>DOCTYPE_static:NEOBLOG</query></delete>&commit=true';
		 
		$this->doRequest($clearnSolrIndexUrl);
	
		Mage::app()->getCache()->clean('matchingAnyTag', array('solrbridge_solrsearch'));
	}
	
	public function truncateCmsPageIndexTables( $solrcore )
	{
		//Need to implement
	}
	
	public function truncateBlogIndexTables( $solrcore )
	{
		
	}
	
	public function getTotalCmsPageDocumentsByCore( $solrcore )
	{
		$solrServerUrl =$this->getSolrServerUrl();
		$queryUrl = trim($solrServerUrl,'/').'/'.$solrcore.'/select/?q=*:*&fl=products_id,store_id&start=0&rows=-1&fq=DOCTYPE_static:CMSPAGE&wt=json';
		$result = $this->doRequest($queryUrl);
		if( isset($result['response']['numFound']) )
		{
			return $result['response']['numFound'];
		}
		return 0;
	}
	
	public function getTotalBlogDocumentsByCore( $solrcore )
	{
		$solrServerUrl =$this->getSolrServerUrl();
		$queryUrl = trim($solrServerUrl,'/').'/'.$solrcore.'/select/?q=*:*&fl=products_id,store_id&start=0&rows=-1&fq=DOCTYPE_static:NEOBLOG&wt=json';
		$result = $this->doRequest($queryUrl);
		if( isset($result['response']['numFound']) )
		{
			return $result['response']['numFound'];
		}
		return 0;
	}
	
	/**
	 * Post json data to Solr Server for Indexing
	 * @param string $jsonData
	 * @param string $updateurl
	 * @param string $solrcore
	 * @return number
	 */
	public function postCmsPageJsonData($jsonData, $updateurl, $solrcore)
	{
	
		if (!function_exists('curl_init')){
			echo 'CURL have not installed yet in this server, this caused the Solr index data out of date.';
			exit;
		}else{
			if(!isset($jsonData) && empty($jsonData)) {
				return 0;
			}
	
			$postFields = array('stream.body'=>$jsonData);
	
			$output = $this->doRequest($updateurl, $postFields);
	
			if (isset($output['responseHeader']['QTime']) && intval($output['responseHeader']['QTime']) > 0)
			{
				return $this->getTotalCmsPageDocumentsByCore($solrcore);
			}else {
				return 0;
			}
		}
	}
	/**
	 * Post json data to Solr Server for Indexing
	 * @param string $jsonData
	 * @param string $updateurl
	 * @param string $solrcore
	 * @return number
	 */
	public function postBlogJsonData($jsonData, $updateurl, $solrcore)
	{
	
		if (!function_exists('curl_init')){
			echo 'CURL have not installed yet in this server, this caused the Solr index data out of date.';
			exit;
		}else{
			if(!isset($jsonData) && empty($jsonData)) {
				return 0;
			}
	
			$postFields = array('stream.body'=>$jsonData);
	
			$output = $this->doRequest($updateurl, $postFields);
	
			if (isset($output['responseHeader']['QTime']) && intval($output['responseHeader']['QTime']) > 0)
			{
				return $this->getTotalBlogDocumentsByCore($solrcore);
			}else {
				return 0;
			}
		}
	}
}