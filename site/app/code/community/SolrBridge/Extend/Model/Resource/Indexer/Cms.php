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
class SolrBridge_Extend_Model_Resource_Indexer_Cms extends SolrBridge_Solrsearch_Model_Resource_Indexer
{
	public $totalFetchedCmsPages = 0;
	public $totalFetchedCmsPagesByStore = 0;
	public $totalMagentoCmsPagesNeedToUpdate = 0;
	public $utility;
	public $totalMagentoCmsPages = 0;
	
	public function _construct(){
		$this->utility = Mage::getSingleton('sbsext/utility');
	}
	/**
	 * Prepare collection meta data for index (1)
	 * @param string $solrcore
	 * @return array
	 */
	public function prepareCollectionMetaData($solrcore)
	{
		$this->collectionMetaData = $this->utility->getCMSPageCollectionMetaData($solrcore);
	}
	/**
	 * Start collection parameters requied for indexing
	 * @param array $requestData
	 * @return SolrBridge_Solrsearch_Model_Resource_Indexer
	 */
	public function start($requestData)
	{
		$this->request = $requestData;
	
		$this->messages = array();
	
		$this->solrServerUrl = Mage::getResourceModel('sbsext/solr')->getSolrServerUrl();
	
		//get count of how many process executed
		if ( isset($this->request['count']) )
		{
			$this->count = ($this->request['count'] + 1);
		}
	
		if ( isset($this->request['solrcore']) && !empty($this->request['solrcore']))
		{
			$this->solrcore = $this->request['solrcore'];
		}
	
		//get status NEW/UPDATE/TRUNCATE/CONTINUE
		$this->status = 'NEW';
		if ( isset($this->request['status']) && !empty($this->request['status']))
		{
			$this->status = $this->request['status'];
		}
		//Pick action
		if ( isset($this->request['action']) && !empty($this->request['action']))
		{
			$this->action = $this->request['action'];
		}
	
		//get page
		if ( isset($this->request['page']) && !empty($this->request['page']))
		{
			$this->page = $this->request['page'];
		}
		
		if ($this->action !== 'TRUNCATE')
		{
			$this->messages[] = Mage::helper('sbsext')->__("#{$this->count}------------------------------------------------");
		}

		/**
		 * get solr core
		 * check if the solr core exist for the first time
		 */
		if ( isset($this->solrcore) && !empty($this->solrcore) && $this->status === 'NEW')
		{
			$availableCores = $this->utility->getAvailableCores();
	
			if ( !isset($availableCores[$this->solrcore]) )
			{
				$this->setStatus('ERROR');
				$this->messages[] = Mage::helper('sbsext')->__('The solr core %s not found', $this->solrcore);
				return $this;
			}
	
			if ( !Mage::getResourceModel('sbsext/solr')->pingSolrCore($this->solrcore) )
			{
				$this->setStatus('ERROR');
				$this->messages[] = Mage::helper('sbsext')->__('Failed to ping Solr Server with core %s', $this->solrcore);
				return $this;
			}
			else
			{
				$this->messages[] = Mage::helper('sbsext')->__('Ping Solr Server with core %s successfully...', $this->solrcore);
			}
		}
	
		//Set current store id
		if ( isset($this->request['currentstoreid']) && !empty($this->request['currentstoreid']) )
		{
			$this->currentStoreId = $this->request['currentstoreid'];
		}
	
		$itemsPerCommitConfig = $this->getSetting('items_per_commit');
		
		if( intval($itemsPerCommitConfig) > 0 )
		{
			$this->itemsPerCommit = $itemsPerCommitConfig;
		}
		//Pick totalfetchedproducts
		if (isset($this->request['totalfetchedcmspages']))
		{
			$this->totalFetchedCmsPages = $this->request['totalfetchedcmspages'];
		}
		
		if (isset($this->request['totalfetchedcmspagesbystore']))
		{
			$this->totalFetchedCmsPagesByStore = $this->request['totalfetchedcmspagesbystore'];
		}
		//Pick start time
		$this->starttime = time();
		if (isset($this->request['starttime']))
		{
			$this->starttime = $this->request['starttime'];
		}
		
		if (isset($this->request['totalMagentoCmsPagesNeedToUpdate']))
		{
			$this->totalMagentoCmsPagesNeedToUpdate = $this->request['totalMagentoCmsPagesNeedToUpdate'];
		}
	
		//Assign store ids as array to $this->storeids for later use
		$this->storeids = $this->utility->getMappedStoreIdsFromSolrCore($this->solrcore);
	
		//Set current store id
		if ( isset($this->request['storeids']) && !empty($this->request['storeids']))
		{
			$this->storeids = explode(',', $this->request['storeids']);
		}
	}
	
	/**
	 * Execute index process
	 * @return SolrBridge_Solrsearch_Model_Resource_Indexer
	 */
	public function execute()
	{
		//Prepare collection metadata
		$this->prepareCollectionMetaData($this->solrcore);
	
		$this->totalCmsPageSolrDocuments = (int) Mage::helper('sbsext')->getTotalCmsPageDocumentsByCore($this->solrcore);
		$this->totalMagentoCmsPages = (int) $this->collectionMetaData['totalCmsPageCount'];
	
		/*
		if ($this->totalCmsPageSolrDocuments >= $this->totalMagentoCmsPages)
		{
			Mage::getResourceModel('solrsearch/solr')->synchronizeData($this->solrcore);
			$this->totalSolrDocuments = (int) Mage::helper('solrsearch')->getTotalDocumentsByCore($this->solrcore);
		}
		*/
	
		$this->loadedStores = $this->collectionMetaData['loadedStores'];
		$this->loadedStoresName = $this->collectionMetaData['loadedStoresName'];
		
		if (!$this->totalFetchedCmsPages)
		{
			$this->messages[] = Mage::helper('sbsext')->__('Start indexing process for core (%s)', $this->solrcore);
		}
	
		$this->messages[] = Mage::helper('sbsext')->__('Magento cms page count : %s', $this->totalMagentoCmsPages);
	
		$this->messages[] = Mage::helper('sbsext')->__('Existing solr documents : %s', $this->totalCmsPageSolrDocuments);
	
		if ( $this->action == 'REINDEXCMSPAGE' ) // There is no any solr document exists
		{
			if ($this->status == 'NEW')
			{
				$this->truncateIndex();
			}
			$this->reindexSolrData();
			return $this;
		}
	}
	
	/**
     * Process New Indexing
     * @param string $core
     */
    public function reindexSolrData($reindexPrice = false)
    {
    	$this->messages[] = Mage::helper('sbsext')->__('Magento stores mapped ('.count($this->loadedStoresName).'): '.@implode(', ', $this->loadedStoresName));

    	if (empty($this->currentStoreId)){
    		$this->currentStoreId = array_shift($this->storeids);
    	}

    	if (!empty($this->currentStoreId) && isset($this->loadedStores[$this->currentStoreId]))
    	{
    		$store = $this->loadedStores[$this->currentStoreId];
    		$storeid = $this->currentStoreId;

    		//Total products number of current store $storeid
    		$totalMagentoCmsPagesByStore = $this->collectionMetaData['stores'][$storeid]['cmspageCount'];

    		//Fetching products from Magento Database
    		$cmsPageCollection = $this->collectionMetaData['stores'][$storeid]['collection'];

    		$cmsPageCollection->clear();
    		
    		

    		$cmsPageCollection->getSelect()->limitPage(intval($this->page),$this->itemsPerCommit);
    		
    		//die($cmsPageCollection->getSelect());
    		
    		$this->messages[] = $cmsPageCollection->getSelect();
    		
    		

    		//Parse json data from product collection
    		$dataArray = $this->utility->parseCmsPageJsonData($cmsPageCollection, $store);

    		$jsonData = $dataArray['jsondata'];

    		$this->totalFetchedCmsPages = ($this->totalFetchedCmsPages + $dataArray['fetchedCmsPages']);

    		$this->totalFetchedCmsPagesByStore = ($this->totalFetchedCmsPagesByStore + $dataArray['fetchedCmsPages']);

    		/*
    		if ($this->totalFetchedProducts > $this->totalMagentoProducts)
    		{
    			$this->totalFetchedProducts = $this->totalMagentoProducts;
    		}
    		*/

    		$this->percent = $this->calculatePercent($this->totalMagentoCmsPages, $this->totalFetchedCmsPages);

    		//Post json data to Solr
    		$numberOfIndexedDocuments = $this->postJsonData($jsonData);

    		unset($jsonData);

    		$this->page = ($this->page + 1);

    		if ($numberOfIndexedDocuments > 0)
    		{
    			$this->totalCmsPageSolrDocuments = $numberOfIndexedDocuments;
    		}
    		if ($this->totalFetchedCmsPagesByStore >= $totalMagentoCmsPagesByStore)
    		{
    			$this->currentStoreId = array_shift($this->storeids);
    			$this->totalFetchedCmsPagesByStore = 0;
    			$this->page = 1;
    		}

    		$this->prepareIndexProgressMessage($store, $this->totalFetchedCmsPagesByStore, $totalMagentoCmsPagesByStore);
    	}
    }
    /**
     * Prepare index progress message
     * @param unknown $store
     * @param int $totalMagentoProductsByStore
     * @param boolean $update
     */
    public function prepareIndexProgressMessage($store, $totalMagentoCmsPageByStore, $update = false)
    {
    	if ($update) {
    		$this->messages[] = Mage::helper('sbsext')->__('Posted %s/%s/%s cms pages to Solr', $this->totalFetchedCmsPages, $totalMagentoCmsPageByStore, $this->totalMagentoCmsPages);
    	}else{
    		$this->messages[] = Mage::helper('sbsext')->__('Posted %s/%s cms pages to Solr', $this->totalFetchedCmsPages, $this->totalMagentoCmsPages);
    	}
    
    	$this->messages[] = Mage::helper('sbsext')->__('Current Solr indexed: %s documents/%s cms pages', $this->totalCmsPageSolrDocuments, $this->totalMagentoCmsPages);
    
    	$this->messages[] = Mage::helper('sbsext')->__('Progress: %s', $this->percent.'%');
    
    	$this->messages[] = Mage::helper('sbsext')->__('Total fetch cms pages: %s', $this->totalFetchedCmsPages);
    	$this->messages[] = Mage::helper('sbsext')->__('Total fetch cms pages from store '.$store->getName().': %s', $this->totalFetchedCmsPagesByStore);
    
    	$this->messages[] = Mage::helper('sbsext')->__('Current store: %s(%s/%s cms pages)', $store->getName(), $this->totalFetchedCmsPagesByStore, $totalMagentoCmsPageByStore);
    }
	/**
	 * Truncate Solr Data Index
	 * @param string $solrcore
	 */
	public function truncateIndex()
	{
		$starttime = time();
		$this->totalSolrDocuments = (int) Mage::helper('sbsext')->getTotalCmsPageDocumentsByCore( $this->solrcore );
		Mage::getResourceModel('sbsext/solr')->truncateCmsPageSolrDocuments( $this->solrcore );
		sleep(2);
		while( $currentCmsPageSolrDocuments = Mage::helper('sbsext')->getTotalCmsPageDocumentsByCore( $this->solrcore ) > 0 )
		{
			$endtime = time();
			//Terminate the script if it takes over 1 minute
			if (($endtime - $starttime) > 300 && $currentCmsPageSolrDocuments > 0)
			{
				$this->messages[] = Mage::helper('sbsext')->__('The script is terminated because it takes so long...');
				return $this;
			}
		}
	
		if($currentCmsPageSolrDocuments < 1)
		{
			Mage::getResourceModel('sbsext/solr')->truncateCmsPageIndexTables($this->solrcore);
			$this->messages[] = Mage::helper('solrsearch')->__('Truncate %s cms page documents from core (%s) successfully', $this->totalCmsPageSolrDocuments, $this->solrcore);
			$this->setStatus('FINISH');
			$this->percent = 100;
		}
		else
		{
			$$this->messages[] = Mage::helper('solrsearch')->__('There should be problem with the Solr server, please try to restart the solr server and try it again...');
		}
		return $this;
	}
	
	/**
	 * Post solrdata to solr
	 * @param string $jsonData
	 * @return int
	 */
	public function postJsonData($jsonData)
	{
		$postNumber = ($this->totalFetchedCmsPages > $this->itemsPerCommit)?$this->itemsPerCommit:$this->totalFetchedCmsPages;
		$updateurl = trim($this->solrServerUrl,'/').'/'.$this->solrcore.'/update/json?wt=json';
		$this->messages[] = Mage::helper('solrsearch')->__('Started posting json of %s cms pages to Solr', $postNumber);
		return Mage::getResourceModel('sbsext/solr')->postCmsPageJsonData($jsonData, $updateurl, $this->solrcore);
	}
	
	/**
	 * Check Index status to see how many solr documents indexed
	 */
	public function checkIndexStatus()
	{
		//echo $this->totalMagentoCmsPages;
		//die();
		$this->totalCmsPageSolrDocuments = (int) Mage::helper('sbsext')->getTotalCmsPageDocumentsByCore($this->solrcore);
		if ($this->totalCmsPageSolrDocuments >= $this->totalMagentoCmsPages) {
			$this->messages[] = Mage::helper('solrsearch')->__('Indexed %s cms pages into Solr core (%s) successfully', $this->totalMagentoCmsPages, $this->solrcore);
			$this->setStatus('FINISH');
			$this->percent = 100;
		}else{
			$this->setStatus('CONTINUE');
		}
	}
	
	/**
	 * Prepare response data
	 */
	public function prepareResponseData()
	{
		$this->response['page'] = $this->page;
		$this->response['status'] = $this->status;
		$this->response['solrcore'] = $this->solrcore;
		$this->response['percent'] = $this->percent;
		$this->response['currentstoreid'] = (int)$this->currentStoreId;
		$this->response['totalsolrdocuments'] = $this->totalCmsPageSolrDocuments;
		$this->response['totalmagentocmspages'] = $this->totalMagentoCmsPages;
		$this->response['totalfetchedcmspages'] = $this->totalFetchedCmsPages;
		$this->response['remaincmspages'] = ($this->totalMagentoCmsPages - $this->totalFetchedCmsPages);
		$this->response['totalMagentoCmsPagesNeedToUpdate'] = $this->totalMagentoCmsPagesNeedToUpdate;
		$this->response['storeids'] = @implode(',', $this->storeids);
		$this->response['action'] = $this->action;
		$this->response['totalfetchedcmspagesbystore'] = $this->totalFetchedCmsPagesByStore;
		$this->response['message'] = $this->messages;
		$this->response['count'] = $this->count;
	
		$this->messages[] = Mage::helper('sbsext')->__('Current store ids: %s', @implode(', ', $this->storeids));
	}
	/**
	 * End the process
	 * @return array
	 */
	public function end()
	{
		if ($this->totalFetchedCmsPages == $this->totalMagentoCmsPages && $this->action !== 'TRUNCATE') {
			sleep(3);
			$totalSolrDocs = (int) Mage::helper('sbsext')->getTotalCmsPageDocumentsByCore($this->solrcore);
			if ($totalSolrDocs < $this->totalMagentoCmsPages) {
				$this->messages = array();
				$this->messages[] = Mage::helper('sbsext')->__('Waiting until solr indexed all cms pages ........');
	
				$this->status = 'WAITING';
	
				$this->prepareResponseData();
	
				return $this->response;
			}
		}
	
		$this->endtime = time();
		$this->seconds = ($this->endtime - $this->starttime);
	
	
		if ($this->action !== 'TRUNCATE') {
			$this->response['remaintime'] = $this->calculateRemainTime();
			$this->messages[] = Mage::helper('sbsext')->__(ucfirst($this->status).'...');
		}
	
		$this->prepareResponseData();
	
		return $this->response;
	}
}