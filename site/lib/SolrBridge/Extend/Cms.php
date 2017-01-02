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
class SolrBridge_Extend_Cms extends SolrBridge_Solr
{
	/**
	 * Prepare solr filter query paprams
	 */
	protected function prepareFilterQuery()
	{
		$filterQuery = array();
	
		$storeid = $this->getParam('storeid');
	
		$config = $this->getConfig();
	
		$websiteid = isset($config['stores'][$storeid]['website_id'])?$config['stores'][$storeid]['website_id']:0;
	
		$checkInstock = (int) $this->getConfigValue('check_instock');
	
		$filterQuery = array_merge($filterQuery, array(
				'store_id' => array($storeid),
				'website_id' => array($websiteid),
				'DOCTYPE_static' => array('CMSPAGE')
		));
	
		if ($checkInstock > 0) {
			$filterQuery['instock_int'] = array(1);
		}
	
		$filterQueryArray = array();
	
		foreach($filterQuery as $key=>$filterItem)
		{
	
			if(count($filterItem) > 0){
				$query = '';
				foreach($filterItem as $value){
					$query .= $key.':%22'.urlencode(trim(addslashes($value))).'%22+OR+';
				}
	
				$query = trim($query, '+OR+');
	
				$filterQueryArray[] = $query;
			}
		}
	
		$filterQueryString = '';
	
		if(count($filterQueryArray) > 0) {
			if(count($filterQueryArray) < 2) {
				$filterQueryString .= $filterQueryArray[0];
			}else{
				$filterQueryString .= '%28'.@implode('%29+AND+%28', $filterQueryArray).'%29';
			}
		}
	
		$this->filterQuery = $filterQueryString;
	}
}