<?php
/*
PHP implementation of PrintGrid Cloud Print
Author, Sagar Kohli
 */

require_once 'HttpRequest.Class.php';

class PrintGridCloudPrint {
		
	const PRINTERS_SEARCH_URL = "https://api.printgrid.io/api/Printer/All";
	const PRINT_URL = "https://api.printgrid.io/api/PrintJobs/Submit";
	const Refresh_Token_Url = 'https://auth.printgrid.io/connect/token';
	
	var $config = array(
        'client_id' 	=> 'PrintGrid.ExternalAuthClient', //Do not change this
        'client_secret' => 'EDC8E3C7-C007-4F91-B805-39500B59ED4D', //Do not change this
		'grant_type' => "refresh_token"
		);

	private $authtoken;
	private $httpRequest;
	private $refreshtoken;
	
	/**
	 * Function __construct
	 * Set private members varials to blank
	 */
	public function __construct() {
		
		$this->authtoken = "";
		$this->httpRequest = new HttpRequest();
	}
	
	/**
	 * Function setAuthToken
	 *
	 * Set auth tokem
	 * @param string $token token to set
	 */
	public function setAuthToken($token) {
		$this->authtoken = $token;
	}
	
	/**
	 * Function getAuthToken
	 *
	 * Get auth tokem
	 * return auth token
	 */
	public function getAuthToken() {
		return $this->authtoken;
	}
	
	
	/**
	 * Function getAccessTokenByRefreshToken
	 *
	 * Gets access token by making http request
	 * 
	 * @param $url url to post data to
	 * 
	 * @param $post_fields post fileds array
	 * 
	 * return access tokem
	 */
	
	public function getAccessTokenByRefreshToken($refreshToken) {
		$this->config['refresh_token'] = $refreshToken;
		$responseObj =  $this->getAccessToken(self::Refresh_Token_Url, http_build_query($this->config));
		if(!isset($responseObj->access_token))
			die('Invalid refresh token');
		else
			return $responseObj->access_token;
	}
	
	
	/**
	 * Function getAccessToken
	 *
	 * Makes Http request call
	 * 
	 * @param $url url to post data to
	 * 
	 * @param $post_fields post fileds array
	 * 
	 * return http response
	 */
	public function getAccessToken($url,$post_fields) {
		$this->httpRequest->setUrl($url);
		$this->httpRequest->setPostData($post_fields);
		if (!$this->httpRequest->send()) {
			return null;
		}
		$response = json_decode($this->httpRequest->getResponse());
		return $response;
	}
	
	/**
     * Function getPrinters
     * Get all the printers of a user on PrintGrid Cloud Print. 
     */
	public function getPrinters() {
		
		// Check if we have auth token
		if(empty($this->authtoken)) {
			// We don't have auth token so throw exception
			throw new Exception("Please first login to PrintGrid");
		}
        $authheaders = array(
            "Authorization: Bearer " .$this->authtoken,
			"Accept: application/json"
		);

		$this->httpRequest = new HttpRequest();
		$this->httpRequest->setUrl(self::PRINTERS_SEARCH_URL);
		$this->httpRequest->setHeaders($authheaders);
		if (!$this->httpRequest->send()) {
			return array();
		}
		$responsedata = $this->httpRequest->getResponse();
		$printers = json_decode($responsedata, true);

		if(is_null($printers)) {
			return array();
		}
		else {
			return $printers;
		}
	}
	
/**
     * Function sendPrintToPrinter
     * 
     * Sends document to the printer
     * 
     * @param Printer id $printerid    // Printer id returned by PrintGrid Cloud Print service
     * 
     * @param Job Title $printjobtitle // Title of the print Job
     * 
     * @param File Path $filepath      // Path to the file to be send to PrintGrid Cloud Print
     * 
     * @param Content Type $contenttype // File content type e.g. application/pdf (Only PDF supported for now)
     */
	public function sendPrintToPrinter($printerid, $category, $title, $filepath, $contenttype) {
		
		 // Check if we have auth token
		if(empty($this->authtoken)) {
			// We don't have auth token so throw exception
			throw new Exception("Please first login to Google by calling loginToGoogle function");
		}
		// Check if prtinter id is passed
		if(empty($printerid)) {
			// Printer id is not there so throw exception
			throw new Exception("Please provide printer ID");	
		}
		
		// Open the file which needs to be print
		$handle = fopen($filepath, "rb");
		if(!$handle)
		{
			// Can't locate file so throw exception
			throw new Exception("Could not read the file. Please check file path.");
		}
		// Read file content
		$contents = fread($handle, filesize($filepath));
		fclose($handle);
		
		// Prepare post fields for sending print
		$post_fields = array(
			'printer_id' => $printerid,
			'category' => $category,
			'title' => $title,
			'content_transfer_encoding' => 'base64',
			'content' => base64_encode($contents), // encode file content as base64
			'content_type' => $contenttype		
		);

		$authheaders = array(
			"Authorization: Bearer " . $this->authtoken,
			"Content-Type: application/json"
		);

		// Make http call for sending print Job
		$this->httpRequest = new HttpRequest();
		$this->httpRequest->setUrl(self::PRINT_URL);
		$this->httpRequest->setPostData(json_encode($post_fields));
		$this->httpRequest->setHeaders($authheaders);
		$this->httpRequest->send();
		$response = json_decode($this->httpRequest->getResponse());

        // Has document been successfully sent?
        if($response->success == true) 
		{
            return array('status' =>true,'errorcode' =>'','errormessage'=>"", 'id' => $response->job_id);
        }
        else
		{
            return array('status' =>false,'errorcode' =>'','errormessage'=>'Failed to send job');
        }
	}
}
