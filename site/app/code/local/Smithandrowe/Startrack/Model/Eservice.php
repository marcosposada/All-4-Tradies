<?php
$lib = Mage::getModuleDir('', 'Smithandrowe_Startrack') . DS . 'Model' . DS . 'WSSoapClient.php';
require_once($lib);

define("ERRORSTRING", "*Error*");

class Smithandrowe_Startrack_Model_Eservice
{	
	
    public function invokeWebService(array $connection, $operation, array $request)
	{
			try
			{		
				$clientArguments = array(
											'exceptions' => true,			
											'encoding' => 'UTF-8',
											'soap_version' => SOAP_1_1,
											'features' => SOAP_SINGLE_ELEMENT_ARRAYS
									 	);

				$oClient = new Smithandrowe_Startrack_Model_WSSoapClient($connection['wsdlFilespec'], $clientArguments);
				
				$oClient->__setUsernameToken($connection['username'], $connection['password']);	

				return $oClient->__soapCall($operation, $request);																				
			}
			catch (SoapFault $e)
			{
				throw new SoapFault($e->faultcode, $e->faultstring, NULL);
			}
	}
	
	public function statusDescription($statusCode, $level, $verbosity)
	{	
		if (is_null($statusCode))
		{
			return "";
		}
		switch (strtolower($level))
		{
    		case 'consignment':
    				switch (strtolower($verbosity))
					{
						case 'brief':
							$descriptionMap = $this->consignmentBriefDescriptionMap();
						break;
						case 'full':
							$descriptionMap = $this->consignmentFullDescriptionMap();
						break;
						default:
							return ERRORSTRING;
					}
        	break;
    		case 'freightitem':
					switch (strtolower($verbosity))
					{
						case 'brief':
							$descriptionMap = $this->freightItemBriefDescriptionMap();
						break;
						case 'full':
							$descriptionMap = $this->freightItemFullDescriptionMap();
						break;
						default:
							return ERRORSTRING;
					}
        	break;
    		default:
        		return ERRORSTRING			;
		}
		
		$returnVal = $descriptionMap[strtoupper($statusCode)];
		if (is_null($returnVal))
		{
			$returnVal = "";
		}
		return $returnVal;

	}
	
	public function serviceDescription($serviceCode)
	{
		if (is_null($serviceCode))
		{
			return "";
		}
		$descriptionMap = $this->serviceDescriptionMap();
		$returnVal = $descriptionMap[strtoupper($serviceCode)];
		if (is_null($returnVal))
		{
			$returnVal = "";
		}
		return $returnVal;
	}
	
	public function locationDescription($locationCode)
	{
		if (is_null($locationCode))
		{
			return "";
		}

		$descriptionMap = $this->locationDescriptionMap();
		$returnVal = $descriptionMap[strtoupper($locationCode)];
		if (is_null($returnVal))
		{
			$returnVal = "";
		}
		return $returnVal;
	}
	
	public function qualityControlDescription($qcCode)
	{
		if (is_null($qcCode))
		{
			return "";
		}
		$descriptionMap = $this->qualityControlDescriptionMap();
		$returnVal = $descriptionMap[strtoupper($qcCode)];
		if (is_null($returnVal))
		{
			$returnVal = "";
		}
		return $returnVal;
	}
	
	public function podSignatoryDescription($signatoryCode)
	{
		if (is_null($signatoryCode))
		{
			return "";
		}
		$signatoryCode = trim($signatoryCode);
		$descriptionMap = $this->podSignatoryDescriptionMap();
		$returnVal = $descriptionMap[strtoupper($signatoryCode)];
		if (is_null($returnVal))
		{
			$returnVal = "";
		}
		return $returnVal;
	}
	
	public function substituteAnyPODSignatoryCode($podSignatoryName)
	{
		if(substr($podSignatoryName, 0, 1) == '*')
		{
			$returnVal = $this->podSignatoryDescription($podSignatoryName);
			if ($returnVal == "")
			{
				return $podSignatoryName;
			}
			return $returnVal;
		}
		else
		{
			return $podSignatoryName;
		}			
	}
	
	public function stateAbbreviation($stateCode)
	{
		if (is_null($stateCode))
		{
			return "";
		}
		$abbreviationMap = $this->stateAbbreviationMap();
		$returnVal = $abbreviationMap[strtoupper($stateCode)];
		if (is_null($returnVal))
		{
			$returnVal = "";
		}
		return $returnVal;
	}
	
	public function consignmentTransitState($stateCode)
	{
		if (is_null($stateCode))
		{
			return "";
		}
		$abbreviationMap = $this->consignmentTransitStateMap();
		$returnVal = $abbreviationMap[strtoupper($stateCode)];
		if (is_null($returnVal))
		{
			$returnVal = "";
		}
		return $returnVal;
	}
	
	public function freightItemTransitState($stateCode)
	{
		if (is_null($stateCode))
		{
			return "";
		}
		$abbreviationMap = $this->freightItemTransitStateMap();
		$returnVal = $abbreviationMap[strtoupper($stateCode)];
		if (is_null($returnVal))
		{
			$returnVal = "";
		}
		return $returnVal;
	}
	
	public function suburbs()
	{
		return $this->suburbsMap();
	}
	
	public function destinationSortationCode($postcode, $serviceCode)
	{
		return $this->nearestDepot($postcode) . $this->fastServiceCode($serviceCode);
	}
	
	public function nearestDepot($postcode)
	{
		if (is_null($postcode))
		{
			return "";
		}
		$abbreviationMap = $this->nearestDepotMap();
		$returnVal = $abbreviationMap[strtoupper($postcode)];
		if (is_null($returnVal))
		{
			$returnVal = "";
		}
		return $returnVal;
	}
	
	public function fastServiceCode($serviceCode)
	{
		if (is_null($serviceCode))
		{
			return "";
		}
		$abbreviationMap = $this->fastServiceCodeMap();
		$returnVal = $abbreviationMap[strtoupper($serviceCode)];
		if (is_null($returnVal))
		{
			$returnVal = "";
		}
		return $returnVal;
	}
	
	private function consignmentBriefDescriptionMap() 
	{ 
   		return array 
   		( 
			'AD' => 'At Delivery Depot',    
      		'BI' => 'Booked In',  
      		'CO' => 'Confirmed',     
      		'DE' => 'Deleted',   
      		'DF' => 'Delivered in Full', 
      		'DL' => 'Delivered',     
      		'FS' => 'Final Shortage',     
      		'IC' => 'Incomplete',   
      		'IT' => 'In Transit',     
      		'OD' => 'On Board for Delivery',   
      		'PD' => 'Partial Delivery',    
      		'PP' => 'Partial Pickup',  
      		'PU' => 'Picked Up',    
      		'RC' => 'Re-Consigned',   
      		'RD' => 'To be Re-Delivered',
      		'RP' => 'Ready for Pickup',  
      		'UC' => 'Unconfirmed',    
      		'UD' => 'Unsuccessful Delivery'
   		); 
	} 

	private function consignmentFullDescriptionMap() 
	{ 
   		return array 
   		( 
			'AD' => 'Consignment is at carrier depot closest to receiver',    
      		'BI' => 'Consignment held at carrier depot closest to receiver until a date and time authorised by receiver',   
      		'CO' => 'Sender has provided consignment information to carrier in preparation for shipment',     
      		'DE' => 'Sender has deleted consignment information prepared earlier in preparation for shipment',     
      		'DF' => 'All freight items in the consignment have been delivered',     
      		'DL' => 'Some or all freight items in the consignment have been delivered and Proof of Delivery is available',     
      		'FS' => 'Delivery is complete but not all items were able to be delivered',     
      		'IC' => 'Sender has partially completed consignment information for carrier in preparation for shipment',     
      		'IT' => 'Consignment is in transit between two carrier depots (initial/intermediate/final)',     
      		'OD' => 'Consignment is in a local delivery vehicle',     
      		'PD' => 'Some but not all freight items in the consignment have been delivered',     
      		'PP' => 'Some but not all items in the consignment were scanned by carrier on pickup from sender',     
      		'PU' => 'Carrier has picked up from the sender all freight items in the consignment',     
      		'RC' => 'Consignment information incorrect or incomplete: corrected information supplied in a new consignment',     
      		'RD' => 'Consignment has been returned to local carrier depot as undeliverable: to be re-delivered on a following day',
      		'RP' => 'Consignment awaiting pickup by carrier',     
      		'UC' => 'Sender has completed consignment information for carrier in preparation for shipment but has not yet finalised the consignment',     
      		'UD' => 'Consignment could not be delivered' 		
   		); 
	} 

	private function freightItemBriefDescriptionMap()
	{ 
   		return array 
   		( 
			'AD' => 'At Delivery Depot',    
      		'BI' => 'Booked In',  
      		'CO' => 'Confirmed',     
      		'DE' => 'Deleted',   
      		'DF' => 'Item Delivered', 
      		'FS' => 'Final Shortage',     
      		'IC' => 'Incomplete',   
      		'IT' => 'In Transit',     
      		'OD' => 'On Board for Delivery',   
      		'PU' => 'Picked Up',    
      		'RC' => 'Re-Consigned',   
      		'RD' => 'To be Re-Delivered',
      		'RP' => 'Ready for Pickup',  
      		'UC' => 'Unconfirmed',    
      		'UD' => 'Unsuccessful Delivery'
   		); 
	} 

	private function freightItemFullDescriptionMap()
	{ 
   		return array 
   		( 
			'AD' => 'Freight item is at carrier depot closest to receiver',    
      		'BI' => 'Freight item held at carrier depot closest to receiver until a date and time authorised by receiver',  
      		'CO' => 'Sender has provided consignment information to carrier in preparation for shipment',     
      		'DE' => 'Sender has deleted consignment information prepared earlier in preparation for shipment',   
      		'DF' => 'Freight item has been delivered', 
      		'FS' => 'The freight item was not able to be delivered and delivery of the consignment is complete',     
      		'IC' => 'Sender has partially completed consignment information for carrier in preparation for shipment',   
      		'IT' => 'Freight item is in transit between two carrier depots (initial/intermediate/final)',     
      		'OD' => 'Freight item is in a local delivery vehicle',   
      		'PU' => 'Carrier has picked up freight item from the sender',    
      		'RC' => 'Consignment information was incorrect or incomplete: corrected information supplied in a new consignment',   
      		'RD' => 'Freight item has been returned to local carrier depot as undeliverable: to be re-delivered on a following day',
      		'RP' => 'Freight item awaiting pickup by carrier',  
      		'UC' => 'Sender has completed consignment information for carrier in preparation for shipment but has not yet finalised the consignment',
      		'UD' => 'Freight item could not be delivered'
   		); 
	} 

	private function serviceDescriptionMap()
	{ 
		return $this->getJSONArray("ServiceCodes.json");
	} 
	
	private function locationDescriptionMap()
	{ 
		return $this->getJSONArray("Depots.json");
	}
	
	private function qualityControlDescriptionMap()	
	{ 
		return $this->getJSONArray("QCCodes.json");
	}
	
	private function podSignatoryDescriptionMap()	
	{ 
   		return array 
   		( 
			'*CHECK ADDRESS' => 'RECEIVER ADDRESS APPEARS TO BE INCORRECT',
			'*CLOSED' => 'RECEIVER PREMISES CLOSED',
			'*LAI' => 'LEFT AS INSTRUCTED',
			'*OTHER RETURN' => 'RETURNED TO CARRIER DEPOT',
			'*REFUSED DELIV.' => 'RECEIVER REFUSED DELIVERY',
			'*UAS' => 'SIGNATURE NOT REQUIRED - AIR SATCHEL',

			'*UNDELIVERED' => 'UNDELIVERED'
		);
	}
	private function stateAbbreviationMap()	
	{ 
   		return array 
   		( 
			'0' => 'NT',
			'2' => 'NSW',
			'3' => 'VIC',
			'4' => 'QLD',
			'5' => 'SA',
			'6' => 'WA',
			'7' => 'TAS',
			'9' => 'INT',
			'A' => 'ACT',
			'Z' => 'NZ'
		);
	}
	
	private function consignmentTransitStateMap()	
	{ 
   		return array 
   		( 
			'AT' => 'POD Attachment',
			'B' => 'Booked in for Delivery',
			'C' => 'Late Data',
			'D' => 'Delivered',
			'E' => 'Pickup Cancelled',
			'F' => 'Final Shortage',
			'G' => 'Refused - Pending Further Instructions',
			'H' => 'Held',
			'I' => 'Scanned in Transit',
			'IM' => 'POD Image',
			'J' => 'Held at Delivery Depot',
			'L' => 'Label Scanned In Transit',
			'M' => 'On Board for Delivery',
			'N' => 'NZ Scanning',
			'O' => 'POD On File',
			'P' => 'Picked Up',
			'Q' => 'Truck Out',
			'QC' => 'Inspection Quality Control',
			'R' => 'Unsuccessful Delivery',
			'S' => 'Shortage',
			'T' => 'POD Returned',
			'U' => 'Left as Instructed',
			'V' => 'Redeliver',
			'W' => 'Transfer',
			'X' => 'Reconsigned',
			'Y' => 'Returned To Sender',
			'Z' => 'Registered For Bookin'
		);
	}

	private function freightItemTransitStateMap()	
	{ 
   		return array 
   		( 
			'A' => 'Scanned at Delivery Depot',
			'C' => 'Scanned at Control Depot',
			'D' => 'Item Delivered',
			'E' => 'Pickup Cancelled',
			'F' => 'Freight Handling',
			'H' => 'Held',
			'I' => 'Scanned in Transit',
			'J' => 'Held at Delivery Depot',
			'K' => 'Known Not Seen',
			'M' => 'On Board for Delivery',
			'O' => 'POD On File',
			'P' => 'Picked Up',
			'R' => 'Scanned at Receiver Location',
			'S' => 'In Transit',
			'T' => 'POD Returned',
			'U' => 'Unknown (or Uncertain)',
			'W' => 'Transfer',
			'X' => 'Reconsigned',
			'Z' => 'Registered For Bookin'
		);
	}

	private function suburbsMap()
	{
		return $this->getJSONArray("Locations.json");
	}
	
	private function nearestDepotMap()	
	{ 
		return $this->getJSONArray("NearestDepots.json");
	}
	
	private function fastServiceCodeMap()	
	{ 
		return $this->getJSONArray("FastServiceCodes.json");
	}
		
	private function getJSONArray($fileSpec)
	{
		$callback = array('globalJSONCache', 'getJSONFileContents');
		if (is_callable($callback))
		{
			return call_user_func($callback, $fileSpec);
		}
		else
		{
			static $cache = array();
			if (is_null($cache[$fileSpec]))
			{
				$jO = new SecurePath;
				$fullPath = $jO->getSecurePath() . $fileSpec;
				$contents = file_get_contents($fullPath) or die("Problem with JSON file $fullPath");
				$cache[$fileSpec] = json_decode($contents, true);
			}
			return $cache[$fileSpec];
		}
	}
	
}

?>
