<?php

class Smithandrowe_Startrack_Model_WSSoapClient extends Smithandrowe_Startrack_Model_SoapClientCurl
{
	private $username;
	private $password;
	
	private function WsSecurityHeader() {
		$created = gmdate('Y-m-d\TH:i:s\Z');
		$nonce = mt_rand();
        $password = 'AllTr4d13s7138';
		$authentication = '
		<wsse:Security SOAP-ENV:mustUnderstand="1" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
		<wsse:UsernameToken wsu:Id="UsernameToken-1" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
		    <wsse:Username>' . $this->username . '</wsse:Username>
		    <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">' .
				    $password . '</wsse:Password>
		    <wsse:Nonce>' . base64_encode(pack('H*', $nonce)) . '</wsse:Nonce>
		    <wsu:Created xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">' . $created . '</wsu:Created>
		   </wsse:UsernameToken>
		</wsse:Security>
		';
		
		$authValues = new SoapVar($authentication, XSD_ANYXML);
		$header = new SoapHeader("http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd", "Security",$authValues, true);
		
		return $header;
	}
	
	public function __setUsernameToken($username, $password)
	{
		$this->username=$username;
		$this->password=$password;
	}
	
	public function __soapCall($function_name, $arguments, $options=NULL, $input_headers=NULL, &$output_headers=NULL)
	{
		try
		{
			$result = parent::__soapCall($function_name, $arguments, $options, $this->WsSecurityHeader());
			return $result;
		}
		catch (SoapFault $e)
		{
			throw new SoapFault($e->faultcode, $e->faultstring, NULL);
		}
	}
}