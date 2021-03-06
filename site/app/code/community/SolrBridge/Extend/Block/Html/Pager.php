<?php
/**
 * @category SolrBridge
 * @package WebMods_Solrsearch
 * @author    Hau Danh
 * @copyright    Copyright (c) 2011-2013 Solr Bridge (http://www.solrbridge.com)
 *
 */
class SolrBridge_Extend_Block_Html_Pager extends Mage_Page_Block_Html_Pager
{
	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate('solrsearch/extend/html/pager.phtml');
	}

	public function setCollection($collection)
	{
		$this->_collection = $collection;

		$this->_setFrameInitialized(false);

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

	public function isLastPage()
	{
		return $this->getCurrentPage() >= $this->getLastPageNum();
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

	public function getFirstPageUrl()
	{
		return $this->getPageUrl(1);
	}

	public function getPreviousPageUrl()
	{
		return $this->getPageUrl($this->getCurrentPage() - 1);
	}

	public function getNextPageUrl()
	{
		return $this->getPageUrl($this->getCurrentPage() + 1);
	}

	public function getLastPageUrl()
	{
		return $this->getPageUrl($this->getLastPageNum());
	}

	/**
	 * Initialize frame data, such as frame start, frame start etc.
	 *
	 * @return Mage_Page_Block_Html_Pager
	 */
	protected function _initFrame()
	{
		if (!$this->isFrameInitialized()) {
			$start = 0;
			$end = 0;

			$collection = $this->getCollection();

			if ($this->getLastPageNum() <= $this->getFrameLength()) {
				$start = 1;
				$end = $this->getLastPageNum();
			}
			else {
				$half = ceil($this->getFrameLength() / 2);
				if ($this->getCurrentPage() >= $half && $this->getCurrentPage() <= $this->getLastPageNum() - $half) {
					$start  = ($this->getCurrentPage() - $half) + 1;
					$end = ($start + $this->getFrameLength()) - 1;
				}
				elseif ($this->getCurrentPage() < $half) {
					$start  = 1;
					$end = $this->getFrameLength();
				}
				elseif ($this->getCurrentPage() > ($this->getLastPageNum() - $half)) {
					$end = $this->getLastPageNum();
					$start  = $end - $this->getFrameLength() + 1;
				}
			}
			$this->_frameStart = $start;
			$this->_frameEnd = $end;

			$this->_setFrameInitialized(true);
		}

		return $this;
	}
}