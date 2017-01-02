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
class SolrBridge_Extend_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
	public $utility = null;
	public $itemsPerCommit = 50;
	public $threadEnable = false;
	protected $indexer = null;
	protected function _construct()
	{
		$this->utility = Mage::getSingleton ( 'sbsext/utility' );
		
		$action = $this->getRequest ()->getParam ('action');
		
		if ($action == 'REINDEXBLOG')
		{
			$this->indexer = Mage::getResourceModel ( 'sbsext/indexer_blog' );
		}
		else 
		{
			$this->indexer = Mage::getResourceModel ( 'sbsext/indexer_cms' );
		}
		
	
		parent::_construct ();
	}
	
	public function cmspageAction()
	{
		$this->getResponse ()->setHeader ( "Content-Type", "application/json", true );
		
		try
		{
			$request = $this->getRequest ()->getParams ();
			$request ['starttime'] = time ();
			
			if (isset($request ['status']) && $request ['status'] == 'WAITING')
			{
				sleep ( 2 );
				$message = $request['message'];
				$request['message'] = array();
				$request['message'][] = $message;
				$totalSolrDocuments = (int) Mage::helper('sbsext')->getTotalCmsPageDocumentsByCore( $request ['solrcore'] );
				if ($totalSolrDocuments >= (int) $request['totalmagentocmspages'])
				{
					$request['message'][] = Mage::helper('sbsext')->__('Indexed %s cms pages into Solr core (%s) successfully', $request['totalmagentocmspages'], $request ['solrcore']);
					$request['percent'] = 100;
					$request['status'] = 'FINISH';
				}
				else
				{
					$request['message'][] = '.........';
				}
				echo json_encode ( $request );
				exit ();
			}
			
			$this->indexer->start ( $request );
			$this->indexer->execute ();
			$this->indexer->checkIndexStatus ();
			$response = $this->indexer->end ();
			echo json_encode ( $response );
		}
		catch ( Exception $e )
		{
			$errors = array (
					'status' => 'ERROR',
					'message' => array($e->getMessage ())
			);
		
			if(isset($errors['message']) && $errors['message'] == $this->__('Image file was not found.'))
			{
				$errors['status'] = 'CONTINUE';
				$errors['message'] = '';
			}
			$request = $this->getRequest ()->getParams ();
			$errors = array_merge($request, $errors);
		
			echo json_encode ( $errors );
		}
		exit ();
		
	}
	
	public function blogAction()
	{
		$this->getResponse ()->setHeader ( "Content-Type", "application/json", true );
		
		try
		{
			$request = $this->getRequest ()->getParams ();
			$request ['starttime'] = time ();
			
			if (isset($request ['status']) && $request ['status'] == 'WAITING')
			{
				sleep ( 2 );
				$message = $request['message'];
				$request['message'] = array();
				$request['message'][] = $message;
				$totalSolrDocuments = (int) Mage::helper('sbsext')->getTotalBlogDocumentsByCore( $request ['solrcore'] );
				if ($totalSolrDocuments >= (int) $request['totalmagentoblogs'])
				{
					$request['message'][] = Mage::helper('sbsext')->__('Indexed %s blog posts into Solr core (%s) successfully', $request['totalmagentoblogs'], $request ['solrcore']);
					$request['percent'] = 100;
					$request['status'] = 'FINISH';
				}
				else
				{
					$request['message'][] = '.........';
				}
				echo json_encode ( $request );
				exit ();
			}
			
			$this->indexer->start ( $request );
			$this->indexer->execute ();
			$this->indexer->checkIndexStatus ();
			$response = $this->indexer->end ();
			echo json_encode ( $response );
		}
		catch ( Exception $e )
		{
			$errors = array (
					'status' => 'ERROR',
					'message' => array($e->getMessage ())
			);
		
			if(isset($errors['message']) && $errors['message'] == $this->__('Image file was not found.'))
			{
				$errors['status'] = 'CONTINUE';
				$errors['message'] = '';
			}
			$request = $this->getRequest ()->getParams ();
			$errors = array_merge($request, $errors);
		
			echo json_encode ( $errors );
		}
		exit ();
	}
}