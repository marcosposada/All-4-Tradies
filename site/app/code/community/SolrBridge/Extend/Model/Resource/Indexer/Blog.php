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
class SolrBridge_Extend_Model_Resource_Indexer_Blog extends SolrBridge_Solrsearch_Model_Resource_Indexer
{
	public $totalFetchedBlogs = 0;
	public $totalFetchedBlogsByStore = 0;
	public $totalMagentoBlogsNeedToUpdate = 0;
	public $utility;
	public $totalMagentoBlogs = 0;
	
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
		$this->collectionMetaData = $this->utility->getBlogCollectionMetaData($solrcore);
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
		//Pick totalfetchedblogs
		if (isset($this->request['totalfetchedblogs']))
		{
			$this->totalFetchedBlogs = $this->request['totalfetchedblogs'];
		}
		
		if (isset($this->request['totalfetchedblogsbystore']))
		{
			$this->totalFetchedBlogsByStore = $this->request['totalfetchedblogsbystore'];
		}
		//Pick start time
		$this->starttime = time();
		if (isset($this->request['starttime']))
		{
			$this->starttime = $this->request['starttime'];
		}
		
		if (isset($this->request['totalMagentoBlogsNeedToUpdate']))
		{
			$this->totalMagentoBlogsNeedToUpdate = $this->request['totalMagentoBlogsNeedToUpdate'];
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
	
		$this->totalBlogSolrDocuments = (int) Mage::helper('sbsext')->getTotalBlogDocumentsByCore($this->solrcore);
		$this->totalMagentoBlogs = (int) $this->collectionMetaData['totalBlogCount'];
	
		/*
		if ($this->totalCmsPageSolrDocuments >= $this->totalMagentoCmsPages)
		{
			Mage::getResourceModel('solrsearch/solr')->synchronizeData($this->solrcore);
			$this->totalSolrDocuments = (int) Mage::helper('solrsearch')->getTotalDocumentsByCore($this->solrcore);
		}
		*/
	
		$this->loadedStores = $this->collectionMetaData['loadedStores'];
		$this->loadedStoresName = $this->collectionMetaData['loadedStoresName'];
		
		if (!$this->totalFetchedBlogs)
		{
			$this->messages[] = Mage::helper('sbsext')->__('Start indexing process for core (%s)', $this->solrcore);
		}
	
		$this->messages[] = Mage::helper('sbsext')->__('Magento blogs count : %s', $this->totalMagentoBlogs);
	
		$this->messages[] = Mage::helper('sbsext')->__('Existing solr documents : %s', $this->totalBlogSolrDocuments);
	
		if ( $this->action == 'REINDEXBLOG' ) // There is no any solr document exists
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
    		$totalMagentoBlogsByStore = $this->collectionMetaData['stores'][$storeid]['blogCount'];

    		//Fetching products from Magento Database
    		$blogCollection = $this->collectionMetaData['stores'][$storeid]['collection'];

    		$blogCollection->clear();
    		
    		

    		$blogCollection->getSelect()->limitPage(intval($this->page),$this->itemsPerCommit);
    		
    		//die($cmsPageCollection->getSelect());
    		
    		$this->messages[] = $blogCollection->getSelect();
    		
    		

    		//Parse json data from product collection
    		$dataArray = $this->utility->parseBlogJsonData($blogCollection, $store);

    		$jsonData = $dataArray['jsondata'];

    		$this->totalFetchedBlogs = ($this->totalFetchedBlogs + $dataArray['fetchedBlogs']);

    		$this->totalFetchedBlogsByStore = ($this->totalFetchedBlogsByStore + $dataArray['fetchedBlogs']);

    		/*
    		if ($this->totalFetchedProducts > $this->totalMagentoProducts)
    		{
    			$this->totalFetchedProducts = $this->totalMagentoProducts;
    		}
    		*/

    		$this->percent = $this->calculatePercent($this->totalMagentoBlogs, $this->totalFetchedBlogs);

    		//Post json data to Solr
    		$numberOfIndexedDocuments = $this->postJsonData($jsonData);

    		unset($jsonData);

    		$this->page = ($this->page + 1);

    		if ($numberOfIndexedDocuments > 0)
    		{
    			$this->totalBlogSolrDocuments = $numberOfIndexedDocuments;
    		}
    		if ($this->totalFetchedBlogsByStore >= $totalMagentoBlogsByStore)
    		{
    			$this->currentStoreId = array_shift($this->storeids);
    			$this->totalFetchedBlogsByStore = 0;
    			$this->page = 1;
    		}

    		$this->prepareIndexProgressMessage($store, $this->totalFetchedBlogsByStore, $totalMagentoBlogsByStore);
    	}
    }
    /**
     * Prepare index progress message
     * @param unknown $store
     * @param int $totalMagentoProductsByStore
     * @param boolean $update
     */
    public function prepareIndexProgressMessage($store, $totalMagentoBlogByStore, $update = false)
    {
    	if ($update) {
    		$this->messages[] = Mage::helper('sbsext')->__('Posted %s/%s/%s blog posts to Solr', $this->totalFetchedBlogs, $totalMagentoBlogByStore, $this->totalMagentoBlogs);
    	}else{
    		$this->messages[] = Mage::helper('sbsext')->__('Posted %s/%s blog posts to Solr', $this->totalFetchedBlogs, $this->totalMagentoBlogs);
    	}
    
    	$this->messages[] = Mage::helper('sbsext')->__('Current Solr indexed: %s documents/%s blog posts', $this->totalBlogSolrDocuments, $this->totalMagentoBlogs);
    
    	$this->messages[] = Mage::helper('sbsext')->__('Progress: %s', $this->percent.'%');
    
    	$this->messages[] = Mage::helper('sbsext')->__('Total fetch blog posts: %s', $this->totalFetchedBlogs);
    	$this->messages[] = Mage::helper('sbsext')->__('Total fetch blog posts from store '.$store->getName().': %s', $this->totalFetchedBlogsByStore);
    
    	$this->messages[] = Mage::helper('sbsext')->__('Current store: %s(%s/%s blog posts)', $store->getName(), $this->totalFetchedBlogsByStore, $totalMagentoBlogByStore);
    }
	/**
	 * Truncate Solr Data Index
	 * @param string $solrcore
	 */
	public function truncateIndex()
	{
		$starttime = time();
		$this->totalSolrDocuments = (int) Mage::helper('sbsext')->getTotalBlogDocumentsByCore( $this->solrcore );
		Mage::getResourceModel('sbsext/solr')->truncateBlogSolrDocuments( $this->solrcore );
		sleep(2);
		while( $currentBlogSolrDocuments = Mage::helper('sbsext')->getTotalBlogDocumentsByCore( $this->solrcore ) > 0 )
		{
			$endtime = time();
			//Terminate the script if it takes over 1 minute
			if (($endtime - $starttime) > 300 && $currentBlogSolrDocuments > 0)
			{
				$this->messages[] = Mage::helper('sbsext')->__('The script is terminated because it takes so long...');
				return $this;
			}
		}
	
		if($currentBlogSolrDocuments < 1)
		{
			Mage::getResourceModel('sbsext/solr')->truncateBlogIndexTables($this->solrcore);
			$this->messages[] = Mage::helper('sbsext')->__('Truncate %s blog documents from core (%s) successfully', $this->totalBlogSolrDocuments, $this->solrcore);
			$this->setStatus('FINISH');
			$this->percent = 100;
		}
		else
		{
			$$this->messages[] = Mage::helper('sbsext')->__('There should be problem with the Solr server, please try to restart the solr server and try it again...');
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
		$postNumber = ($this->totalFetchedBlogs > $this->itemsPerCommit)?$this->itemsPerCommit:$this->totalFetchedBlogs;
		$updateurl = trim($this->solrServerUrl,'/').'/'.$this->solrcore.'/update/json?wt=json';
		$this->messages[] = Mage::helper('sbsext')->__('Started posting json of %s blog posts to Solr', $postNumber);
		return Mage::getResourceModel('sbsext/solr')->postBlogJsonData($jsonData, $updateurl, $this->solrcore);
	}
	
	/**
	 * Check Index status to see how many solr documents indexed
	 */
	public function checkIndexStatus()
	{
		//echo $this->totalMagentoCmsPages;
		//die();
		$this->totalBlogSolrDocuments = (int) Mage::helper('sbsext')->getTotalBlogDocumentsByCore($this->solrcore);
		if ($this->totalBlogSolrDocuments >= $this->totalMagentoBlogs) {
			$this->messages[] = Mage::helper('sbsext')->__('Indexed %s blog posts into Solr core (%s) successfully', $this->totalMagentoBlogs, $this->solrcore);
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
		$this->response['totalsolrdocuments'] = $this->totalBlogSolrDocuments;
		$this->response['totalmagentoblogs'] = $this->totalMagentoBlogs;
		$this->response['totalfetchedblogs'] = $this->totalFetchedBlogs;
		$this->response['remainblogs'] = ($this->totalMagentoBlogs - $this->totalFetchedBlogs);
		$this->response['totalMagentoBlogsNeedToUpdate'] = $this->totalMagentoBlogsNeedToUpdate;
		$this->response['storeids'] = @implode(',', $this->storeids);
		$this->response['action'] = $this->action;
		$this->response['totalfetchedblogsbystore'] = $this->totalFetchedBlogsByStore;
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
		if ($this->totalFetchedBlogs == $this->totalMagentoBlogs && $this->action !== 'TRUNCATE')
		{
			sleep(3);
			$totalSolrDocs = (int) Mage::helper('sbsext')->getTotalBlogDocumentsByCore($this->solrcore);
			if ($totalSolrDocs < $this->totalMagentoBlogs) {
				$this->messages = array();
				$this->messages[] = Mage::helper('sbsext')->__('Waiting until solr indexed all blog posts ........');
	
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