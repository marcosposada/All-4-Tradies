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
class SolrBridge_Extend_Model_Resource_Rewrite_Solrsearch_Solr extends SolrBridge_Solrsearch_Model_Resource_Solr
{
	/**
	 * Get solr core statistic to find how many documents exist
	 * @param string $solrcore
	 * @return array
	 */
	public function getSolrLuke($solrcore)
	{
		$solrServerUrl =$this->getSolrServerUrl();
		//$queryUrl = trim($solrServerUrl,'/').'/'.$solrcore.'/admin/luke?reportDocCount=true&fl=products_id&wt=json';
		$queryUrl = trim($solrServerUrl,'/').'/'.$solrcore.'/select/?q=*:*&fl=products_id&rows=-1&fq=DOCTYPE_static:PRODUCT&wt=json';
		$result = $this->doRequest($queryUrl);
		 
		 
		$result['index']['numDocs'] = 0;
		if ( isset($result['response']['numFound']) )
		{
			$result['index']['numDocs'] = $result['response']['numFound'];
		}
		 
		return $result;
	}
}