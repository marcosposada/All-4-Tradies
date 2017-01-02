<?php
class Smithandrowe_Startrack_Model_Shipping_Carrier_Express extends Mage_Shipping_Model_Carrier_Abstract
{
	protected  $_code = 'smithandrowe_startrack_express';
	
	public function collectRates(Mage_Shipping_Model_Rate_Request $request)
	{
		if (!Mage::getStoreConfig('carriers/' . $this->_code . '/active')) {
			return false;
		}
		
		$handling = Mage::getStoreConfig('carriers/' . $this->_code . '/handling');
		
		$result = Mage::getModel('shipping/rate_result');
		
		foreach ($this->_getRates($request) as $rate) {
			if (!isset($rate['error'])) {
				$method = $this->_createMethod($rate);
				$result->append($method);
			} else {
				$error = Mage::getModel('shipping/rate_result_error');
				$error->setCarrier($this->_code);
				$error->setCarrierTitle($this->getConfigData('name'));
				$error->setErrorMessage($rate['error']);
				$result->append($error);
			}
		}
		
		return $result;
	}
	
	protected function _getRates(Mage_Shipping_Model_Rate_Request $request)
	{
        $items = $request->getAllItems();
        $global_volume = 0;
        foreach ($items as $item) {
            $productId = $item->getProductId();

            //GET THE PRODUCT MEASUREMENTS
            $length = Mage::getResourceModel('catalog/product')->getAttributeRawValue($productId, 'pack_length_1',1);
            $width = Mage::getResourceModel('catalog/product')->getAttributeRawValue($productId, 'pack_width_1',1);
            $height = Mage::getResourceModel('catalog/product')->getAttributeRawValue($productId, 'pack_height_1',1);

            //GET THE PRODUCT UNITS
            $length_unit = Mage::getResourceModel('catalog/product')->getAttributeRawValue($productId, 'pack_length_unit_1',1);
            $width_unit = Mage::getResourceModel('catalog/product')->getAttributeRawValue($productId, 'pack_width_unit_1',1);
            $height_unit = Mage::getResourceModel('catalog/product')->getAttributeRawValue($productId, 'pack_height_unit_1',1);

            //IF THE ITEM UNITS ARE IN CM, CONVERT TO MM
            if ($length_unit == 'cm') {
                $length = ($length * 10);
            } if ($width_unit == 'cm') {
                $width = ($width * 10);
            } if ($height_unit == 'cm') {
                $height = ($height * 10);
            }

            //CALCULATE TO M
            $length = ($length / 1000);
            $height = ($height / 1000);
            $width = ($width / 1000);

            //Calculate the volume
            $volume = ($length * $height * $width);

            $global_volume = round(($global_volume + $volume),3);
        }
		$methods = array();

		$connection = array(
			'username' => Mage::getStoreConfig('carriers/' . $this->_code . '/apiusername'),
			'password' => Mage::getStoreConfig('carriers/' . $this->_code . '/apipassword'),
			'userAccessKey' => Mage::getStoreConfig('carriers/' . $this->_code . '/apikey'),
			'wsdlFilespec' => Mage::getModuleDir('etc', 'Smithandrowe_Startrack') . DS . 'wsdl' . DS . 'eservices.xml'
		);
		
		$parameters = array(
			'header' => array(
				'source' => 'TEAM',
				'accountNo' => Mage::getStoreConfig('carriers/' . $this->_code . '/account'),
				'userAccessKey' => Mage::getStoreConfig('carriers/' . $this->_code . '/apikey')
			),
			'senderLocation' => array(
				'suburb' => Mage::getStoreConfig('carriers/' . $this->_code . '/city'),
				'postCode' => Mage::getStoreConfig('carriers/' . $this->_code . '/postcode'),
				'state' => strtoupper(Mage::getStoreConfig('carriers/' . $this->_code . '/state'))
			),
			'receiverLocation' => array(
				'suburb' => $request->getDestCity(),
				'postCode' => $request->getDestPostcode(),
				'state' => strtoupper($request->getDestRegionCode())
			),
			'noOfItems' => 1,
			'weight' => ceil($request->getPackageWeight()),
		    'volume' => $global_volume,
			//'volume' => round(($request->getPackageWidth() / 100) * ($request->getPackageHeight() / 100) * ($request->getPackageDepth() / 100), 3),
			'includeTransitWarranty' => Mage::getStoreConfig('carriers/' . $this->_code . '/risk_warranty'),
			'includeFuelSurcharge' => Mage::getStoreConfig('carriers/' . $this->_code . '/fuel_surcharge'),
			'includeSecuritySurcharge' => Mage::getStoreConfig('carriers/' . $this->_code . '/security_surcharge'),
			'transitWarrantyValue' => number_format($request->getPackageValue(), 2),
		);
		
		if (Mage::getStoreConfig('carriers/' . $this->_code . '/estimate')) {
			$parameters += array('choice' => array('despatchDate' => date('Y-m-d', mktime(0,0,0,date("m"),date("d")+1,date("Y")))));
		}
		
		foreach (explode(',', Mage::getStoreConfig('carriers/' . $this->_code . '/servicecodes')) as $service_code) {
			if ($service_code == '1KN' && ceil($request->getPackageWeight()) > 1) continue;
            if ($service_code == '3KN' && ceil($request->getPackageWeight()) > 3) continue;
            if ($service_code == '5KN' && ceil($request->getPackageWeight()) > 5) continue;
			
			$parameters['serviceCode'] = strtoupper($service_code);
			
			$startrack_request = array('parameters' => $parameters);
			
			try {
				$eService = new Smithandrowe_Startrack_Model_Eservice();
				
				$response = $eService->invokeWebService($connection, Mage::getStoreConfig('carriers/' . $this->_code . '/estimate') ? 'calculateCostAndEstimatedTime' : 'calculateCost', $startrack_request);
				
				if ($response->cost == 0.0) {
					continue;
				}
				
				$totalCostExGST = $response->cost;
				if (Mage::getStoreConfig('carriers/' . $this->_code . '/fuel_surcharge')) $totalCostExGST += $response->fuelSurcharge;
				if (Mage::getStoreConfig('carriers/' . $this->_code . '/risk_warranty')) $totalCostExGST += $response->transitWarrantyCharge;
				if (Mage::getStoreConfig('carriers/' . $this->_code . '/security_surcharge')) $totalCostExGST += $response->securitySurcharge;
				
				$description = $this->_getDescription($service_code);
				
				if (Mage::getStoreConfig('carriers/' . $this->_code . '/estimate') && isset($response->eta) && method_exists('DateTime','diff')) {
					$datetime1 = new DateTime(date('Y-m-d'));
					$datetime2 = new DateTime(date('Y-m-d', strtotime(str_replace('T', ' ', $response->eta))));
					$interval = $datetime1->diff($datetime2);
					if ($interval->format('%d') > 1) {
						$description.= ' (est. ' . $interval->format('%d') . ' days delivery)';
					} else {
						$description.= ' (est. ' . $interval->format('%d') . ' day delivery)';
					}
				}

				$methods[] = array(
						'code' => $service_code,
						'title'  => $description,
						'price'  => $this->getFinalPriceWithHandlingFee($totalCostExGST),
						'cost'   => $totalCostExGST
				);
			} catch (SoapFault $e) {
				if (isset($e->detail->fault->fs_message)) {
					$error = $e->detail->fault->fs_message;
				} else {
					$error = $e->faultstring;
				}
				
				if(strpos($error, 'Invalid Receiver Location ') !== false) { 
					$methods[] = array('error' => $error);
				}
				
				break;
			}
		}
		
		return $methods;
	}

	private function _createMethod($rMethod)
	{
		$method = Mage::getModel('shipping/rate_result_method');

		$method->setCarrier($this->_code);
		$method->setCarrierTitle(Mage::getStoreConfig('carriers/' . $this->_code . '/title'));

		$method->setMethod($rMethod['code']);
		$method->setMethodTitle($rMethod['title']);


        $markup_per = '.'.Mage::getStoreConfig('shipping/erpcore/shippingmarkup');;
        $markup = ($rMethod['cost'] * $markup_per);
		$method->setCost($rMethod['cost']);
		//$method->setPrice($markup + $rMethod['price']);
		$method->setPrice($rMethod['price']);
		return $method;
	}
	
	public function getAllowedMethods()
	{
		return array($this->_code => $this->getConfigData('name'));
	}
	
	public function isCityRequired()
	{
		return true;
	}
	
	public function getCityActive()
	{
		return true;
	}
	
	private function _getDescription($service_code) {
		$services = array(
			//'EXP' => Mage::helper('smithandrowe_startrack')->__('Road Express'),
            'EXP' => Mage::helper('smithandrowe_startrack')->__('Standard Delivery'),
			'1KN' => Mage::helper('smithandrowe_startrack')->__('1kg Nationwide'),
			'3KN' => Mage::helper('smithandrowe_startrack')->__('3kg Nationwide'),
			'5KN' => Mage::helper('smithandrowe_startrack')->__('5kg Nationwide'),
			'TSE' => Mage::helper('smithandrowe_startrack')->__('Trade Show Express'),
			'RET' => Mage::helper('smithandrowe_startrack')->__('Road Express - Tailgate'),
			'RE2' => Mage::helper('smithandrowe_startrack')->__('Road Express - 2 Men'),
			'ITL' => Mage::helper('smithandrowe_startrack')->__('International Express Freight'),
			'LO2' => Mage::helper('smithandrowe_startrack')->__('Local Overnight - 2 Men'),
			'LOT' => Mage::helper('smithandrowe_startrack')->__('Local Overnight - Tailgate'),
			'PAC' => Mage::helper('smithandrowe_startrack')->__('Priority Air Service'),
			'SAT' => Mage::helper('smithandrowe_startrack')->__('Saturday Delivery'),
			'SDA' => Mage::helper('smithandrowe_startrack')->__('Sameday StarTrack Courier'),
			'IDS' => Mage::helper('smithandrowe_startrack')->__('International Document Express'),
		);
		
		return $services[$service_code];
	}
}