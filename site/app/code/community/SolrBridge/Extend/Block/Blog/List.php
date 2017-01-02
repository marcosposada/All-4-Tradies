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
class SolrBridge_Extend_Block_Blog_List extends NeoTheme_Blog_Block_Post_List
{
	protected $_solrData = null;
	
	protected $_pager = null;

	public function _prepareLayout()
	{
		$this->_pager = $this->getLayout()->createBlock('sbsext/html_pager', 'search.custom.pager');
		$this->_pager->setAvailableLimit(array(2 => 2, 10 => 10, 20 => 20, 'all' => 'all'));
		//parent::_prepareLayout();
	}


	public function getPagerHtml()
	{
		$solrData = $this->getSolrData();
		$this->_pager->setCollection($solrData);
		return $this->_pager->toHtml();
	}
	
	/**
	 * Retrieve Toolbar block
	 *
	 * @return Mage_Catalog_Block_Product_List_Toolbar
	 */
	public function getToolbarBlock()
	{
		if ($blockName = $this->getToolbarBlockName()) {
			if ($block = $this->getLayout()->getBlock($blockName)) {
				return $block;
			}
		}
		$block = $this->getLayout()->createBlock($this->_defaultToolbarBlock, microtime());
		return $block;
	}
	/*
	public function _beforeHtml()
	{
		$pager = $this->getLayout()->createBlock('sbsext/html_pager', 'custom.pager');
		$pager->setAvailableLimit(array(2 => 2, 10 => 10, 20 => 20, 'all' => 'all'));
		
		$this->setChild('pager', $pager);
	}
	*/
	
	public function getSolrData()
	{
		if ($this->_solrData === null)
		{
			$queryText = Mage::helper('solrsearch')->getParam('q');
				
			$solrModel = Mage::getModel('sbsext/solr_blog');
				
			$this->_solrData = $solrModel->query($queryText);
		}

		return $this->_solrData;
	}
	
	public function getCollection()
	{
		if ($this->_collection == null)
		{
			$this->_solrData = $this->getSolrData();
			
			$documents = array();
			if( isset($this->_solrData['response']['docs']) )
			{
				$documents = $this->_solrData['response']['docs'];
			}
			
			$postIds = array();
			if(is_array($documents) && count($documents) > 0)
			{
				foreach ($documents as $_doc)
				{
					if ( isset($_doc['products_id']) )
					{
						$postIds[] = $_doc['products_id'];
					}
				}
			}
			
			$collection = Mage::getModel('neotheme_blog/post')->getCollection();
			
			$collection->addFieldToFilter('entity_id', array('in' => $postIds));
			
			//$collection->getSelect()->order("find_in_set(main_table.entity_id,'".implode(',',$postIds)."')");
			
			$this->_collection = $collection;
		}
		
		return $this->_collection;
	}
}