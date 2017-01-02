<?php

class Smithandrowe_Startrack_Model_Track {
    protected  $_code = 'smithandrowe_startrack_express';

    public function track($con) {
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
            'consignmentId' => explode(" ", $con)

        );

        $startrack_request = array('parameters' => $parameters);
        $eService = new Smithandrowe_Startrack_Model_Eservice();
        try {
            $response = $eService->invokeWebService($connection, 'getConsignmentDetails', $startrack_request);
        }
        catch (SoapFault $e) {
            Mage::log($e->faultstring);
        }
        $consignments = $response->consignment;

        return $consignments;


    }
}