<?php

    class Smithandrowe_Profile_Model_AbnObserver
    {
        public function __construct()
        {
        }

        public function lookupAbn($abn)
        {
            $customer = Mage::getModel('customer/customer');
            $abn_search_string = $abn;
            $abn_guid = "5aeab508-3954-4428-8c35-fcc23610a027";
            $abnlookup = new abnlookup($abn_guid);
            try {
                //$abn_search_string = Mage::app()->getRequest()->getPost('business_abn');
                $result = $abnlookup->searchByAbn($abn_search_string);

                /**
                 * Check if the ABN is a Sole Trader
                 */

                if ($abn_search_string) {
                    if (!isset($result->ABRPayloadSearchResults->response->businessEntity->mainName)) {
                        if (isset($result->ABRPayloadSearchResults->response->exception)) {
                            $customer->setBusinessName('Not Found');
                        } else {
                            $name = $result->ABRPayloadSearchResults->response->businessEntity->legalName->givenName . ' ' . $result->ABRPayloadSearchResults->response->businessEntity->legalName->familyName;
                            $customer->setBusinessName($name);
                        }
                    }
                    if (isset($result->ABRPayloadSearchResults->response->businessEntity->mainName)) {
                        $name = $result->ABRPayloadSearchResults->response->businessEntity->mainName->organisationName;
                        $customer->setBusinessName($name);
                    }
                }
            } catch (exception $e) {
                throw $e;
            }
            if (isset($name)) {
                return $name;
            }
        }
    }

    class abnlookup extends SoapClient
    {

        private $guid = "5aeab508-3954-4428-8c35-fcc23610a027";

        public function __construct($guid)
        {
            $this->guid = $guid;
            $params = array(
                'soap_version' => SOAP_1_1,
                'exceptions' => true,
                'trace' => 1,
                'cache_wsdl' => WSDL_CACHE_NONE);
            try {
            parent::__construct('http://abr.business.gov.au/abrxmlsearch/ABRXMLSearch.asmx?WSDL', $params);
            } catch (Exception $e) {
                Mage::log($e);
            }
        }

        public function searchByAbn($abn, $historical = 'N')
        {
            Mage::log('Searching by ABN');
            $params = new stdClass();
            $params->searchString = $abn;
            $params->includeHistoricalDetails = $historical;
            $params->authenticationGuid = $this->guid;
            return $this->ABRSearchByABN($params);
        }

        public function searchByName($company_name)
        {
            Mage::log('Searching by name');
            $params = new stdClass();
            $params->externalNameSearch($company_name);
            $params->authenticationGuid = $this->guid;
            return $this->ABRSearchByName($params);
        }


    }
