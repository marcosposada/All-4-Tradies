<?php

class SolrBridge_Extend_Block_List_Toolbar extends Mage_Catalog_Block_Product_List_Toolbar
{
	/**
	 * Init Toolbar
	 *
	 */
	protected function _construct()
	{
		parent::_construct();

		$this->setTemplate('solrsearch/solr/document/list/toolbar.phtml');
	}

	/**
	 * Set collection to pager
	 *
	 * @param Varien_Data_Collection $collection
	 * @return Mage_Catalog_Block_Product_List_Toolbar
	 */
	public function setCollection($collection)
	{
		$this->_collection = $collection;
		return $this;
	}

	public function getFirstNum()
	{
		$collection = $this->getCollection();
		return (int) $collection['responseHeader']['params']['rows'] * ($this->getCurrentPage()-1)+1;
	}

	public function getLastNum()
	{
		$collection = $this->getCollection();
		return (int) $collection['responseHeader']['params']['rows']*($this->getCurrentPage()-1)+count($collection['response']['docs']);
	}

	public function getTotalNum()
	{
		$collection = $this->getCollection();
		return (int) $collection['response']['numFound'];
	}

	public function isFirstPage()
	{
		return $this->getCurrentPage() == 1;
	}
	public function getLastPageNum()
	{
		$collection = $this->getCollection();

		$collectionSize = (int) $collection['response']['numFound'];
		if (0 === $collectionSize) {
			return 1;
		}
		else {
			return ceil($collectionSize / (int) $collection['responseHeader']['params']['rows']);
		}
		return 1;
	}

	/**
	 * Render pagination HTML
	 *
	 * @return string
	 */
	public function getPagerHtml()
	{
		$pagerBlock = $this->getChild('product_list_toolbar_pager');

		if ($pagerBlock instanceof Varien_Object) {

			/* @var $pagerBlock Mage_Page_Block_Html_Pager */
			$pagerBlock->setAvailableLimit($this->getAvailableLimit());

			$pagerBlock->setUseContainer(false)
			->setShowPerPage(false)
			->setShowAmounts(false)
			->setLimitVarName($this->getLimitVarName())
			->setPageVarName($this->getPageVarName())
			->setLimit($this->getLimit())
			->setFrameLength(Mage::getStoreConfig('design/pagination/pagination_frame'))
			->setJump(Mage::getStoreConfig('design/pagination/pagination_frame_skip'))
			->setCollection($this->getCollection());

			return $pagerBlock->toHtml();
		}

		return '';
	}
}
