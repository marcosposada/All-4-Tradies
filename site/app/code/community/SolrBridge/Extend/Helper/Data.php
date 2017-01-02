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
class SolrBridge_Extend_Helper_Data extends SolrBridge_Solrsearch_Helper_Data
{
	/**
	 * Get CMS Page solr documents
	 * @param string $solrcore
	 */
	public function getTotalCmsPageDocumentsByCore( $solrcore )
	{
		return Mage::getResourceModel('sbsext/solr')->getTotalCmsPageDocumentsByCore($solrcore);
	}
	
	public function getTotalBlogDocumentsByCore( $solrcore )
	{
		return Mage::getResourceModel('sbsext/solr')->getTotalBlogDocumentsByCore($solrcore);
	}
	
	public function getAjaxQueryUrl($query = null,$filterQuery = null)
	{
		$uri = '';
		$advanced_autocomplete = (int)$this->getSetting('advanced_autocomplete');
		if ($advanced_autocomplete > 0)
		{
			$uri = trim(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB), '/').'/sbext.php';
		}
		else
		{
			$uri = $this->_getUrl('solrsearch/ajax/query', array(
					'_query' => array(self::QUERY_VAR_NAME => $query, self::FILTER_QUERY_VAR_NAME=>$filterQuery),
					'_secure' => Mage::app()->getFrontController()->getRequest()->isSecure(),
					'_nosid' => true,
			));
		}
		return trim($uri, '/');
	}
}