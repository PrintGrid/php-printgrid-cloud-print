<?php

/*
HTTP Request Utility
*/

class HttpRequest {
        
	public $httpResponse;
	public $ch;
	public $curlErrNo;
	public $curlErr;
        
    /**
	 * Function __construct
	 * Set member variables
	 * @param url $url  // Url to send http request to
	 */
	public function __construct($url = null) {
		$this->ch = curl_init();
	
		curl_setopt( $this->ch, CURLOPT_FOLLOWLOCATION,true);
		curl_setopt( $this->ch, CURLOPT_HEADER,0);
		curl_setopt( $this->ch, CURLOPT_RETURNTRANSFER,true);
		curl_setopt( $this->ch, CURLOPT_SSL_VERIFYPEER, false);
		 curl_setopt( $this->ch, CURLOPT_HTTPAUTH,CURLAUTH_ANY);
		curl_setopt( $this->ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt( $this->ch, CURLOPT_TIMEOUT, 30);
	
		if(isset($url)) {
			$this->setUrl($url);
		}
    }
	
	/**
	 * Function setUrl
	 * Set http request url
	 * @param string $url  // http request url
	 */
	public function setUrl($url) {
		curl_setopt( $this->ch, CURLOPT_URL, $url );
	}

    /**
	 * Function setPostData
	 * Set data to be posted to the url
	 * @param array $params  // Key value pairs of data to be posted
	 */
        public function setPostData( $params ) {
            
            curl_setopt( $this->ch, CURLOPT_POST, true );
            curl_setopt ( $this->ch, CURLOPT_POSTFIELDS,$params);
        }
	
	 /**
	 * Function setHeaders
	 * Set http request headers
	 * @param array $headers  // array containing headers
	 */
	public function setHeaders($headers) {
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
	}
        
    /**
	 * Function send
	 * Send http request
	 * return void
	 */
	public function send() {
		// execute curl
		$this->httpResponse = curl_exec($this->ch);
		$this->curlErrNo = curl_errno($this->ch);
		$this->curlErr = curl_error($this->ch);
		if ($this->curlErrNo) {
			curl_close($this->ch);
			return false;
		}
		return true;
	}
	
	/**
	 * Function getCurlErrNo
	 * return curl_errno
	 */
	public function getCurlErrNo() {
		return $this->curlErrNo;
	}
	
	/**
	 * Function getCurlErr
	 * return curl_error
	 */
	public function getCurlErr() {
		return $this->curlErr;
	}
	
	/**
	 * Function getCurlHandle
	 * return curl handle
	 */	
	public function getCurlHandle() {
		return $this->ch;
	}
        
    /**
	 * Function getResponse
	 * return response of last http request sent
	 * return http response
	 */
	public function getResponse() {
		return $this->httpResponse;
	}
        
    /**
	 * Function __destruct
	 * class destructor
	 */
	public function __destruct() {
		if ($this->ch) {
			curl_close($this->ch);
		}
	}
}

?>