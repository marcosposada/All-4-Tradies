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
class SolrBridge_Extend_Block_Cms_List extends Mage_Core_Block_Template
{
	protected $_solrData = array();
	
	function _prepareLayout()
	{
		$pager = $this->getLayout()->createBlock('page/html_pager', 'custom.pager');
		$pager->setAvailableLimit(array(5 => 5, 10 => 10, 20 => 20, 'all' => 'all'));
		$this->setChild('pager', $pager);
		parent::_prepareLayout();
	}
	
	public function getCollection()
	{
		if ($this->_collection == null) {
			$queryText = Mage::helper('solrsearch')->getParam('q');
				
			$solrModel = Mage::getModel('sbsext/solr_cms');
				
			$this->_solrData = $solrModel->query($queryText);
				
			$documents = array();
			if( isset($this->_solrData['response']['docs']) )
			{
				$documents = $this->_solrData['response']['docs'];
			}
				
			$pageIds = array();
			if(is_array($documents) && count($documents) > 0)
			{
				foreach ($documents as $_doc)
				{
					if ( isset($_doc['products_id']) )
					{
						$pageIds[] = $_doc['products_id'];
					}
				}
			}
				
			$collection = Mage::getModel('cms/page')->getCollection();
			
			$collection->addFieldToFilter('page_id', array('in' => $pageIds));
				
			//$collection->getSelect()->order("find_in_set(main_table.page_id,'".implode(',',$pageIds)."')");
				
			$this->_collection = $collection;
		}
	
		return $this->_collection;
	}
	
	public function softTrim($text, $count, $wrapText='...'){
	
		if(strlen($text)>$count){
			preg_match('/^.{0,' . $count . '}(?:.*?)\b/siu', $text, $matches);
			$text = $matches[0];
		}else{
			$wrapText = '';
		}
		return $text . $wrapText;
	}
}