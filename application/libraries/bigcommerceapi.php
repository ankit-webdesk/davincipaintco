<?php 
class Bigcommerceapi {
	/**
	 *public and private variables 
	 *
	 * @var string stores data for the class
	 */
	static public $_path;
	static private $_user;
	static private $_token;
	static private $_headers;
	/**
	 * Sets $_path, $_user, $_token, $_headers upon class instantiation
	 * 
	 * @param $user, $path, $token required for the class
	 * @return void
	 */
	public function __construct($user = null, $path = null, $token = null) {
		$path = explode('/api/v2/', $path);
		@$this->_path = $path[0];
		@$this->_user = $user;
		@$this->_token = $token;
		$encodedToken = base64_encode(@$this->_user.":".@$this->_token);
		$authHeaderString = 'Authorization: Basic ' . $encodedToken;
		@$this->_headers = array($authHeaderString, 'Accept: application/json','Content-Type: application/json');
	}	
	
	function http_parse_headers($raw_headers)
    {
        $headers = array();
        $key = ''; // [+]

        foreach(explode("\n", $raw_headers) as $i => $h)
        {
            $h = explode(':', $h, 2);

            if (isset($h[1]))
            {
                if (!isset($headers[$h[0]]))
                    $headers[$h[0]] = trim($h[1]);
                elseif (is_array($headers[$h[0]]))
                {
                    // $tmp = array_merge($headers[$h[0]], array(trim($h[1]))); // [-]
                    // $headers[$h[0]] = $tmp; // [-]
                    $headers[$h[0]] = array_merge($headers[$h[0]], array(trim($h[1]))); // [+]
                }
                else
                {
                    // $tmp = array_merge(array($headers[$h[0]]), array(trim($h[1]))); // [-]
                    // $headers[$h[0]] = $tmp; // [-]
                    $headers[$h[0]] = array_merge(array($headers[$h[0]]), array(trim($h[1]))); // [+]
                }

                $key = $h[0]; // [+]
            }
            else // [+]
            { // [+]
                if (substr($h[0], 0, 1) == "\t") // [+]
                    $headers[$key] .= "\r\n\t".trim($h[0]); // [+]
                elseif (!$key) // [+]
                    $headers[0] = trim($h[0]);trim($h[0]); // [+]
            } // [+]
        }

    }

    public function error($body, $url, $json, $type) {
    	global $error;
    	if (isset($json)) {
	    	$results = json_decode($body, true);
			$results = $results[0];
			$results['type'] = $type;
			$results['url'] = $url;
			$results['payload'] = $json;
			$error = $results;
		} else {
			$results = json_decode($body, true);
			$results = $results[0];
			$results['type'] = $type;
			$results['url'] = $url;
			$error = $results;
		}
    }
	/**
	 * Performs a get request to the instantiated class
	 * 
	 * Accepts the resource to perform the request on
	 * 
	 * @param $resource string $resource a string to perform get on
	 * @return results or var_dump error
	 */
	public function get($resource) {
		$url = $this->_path . '/api/v2' . $resource;
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $this->_headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_VERBOSE, 1);
		curl_setopt($curl, CURLOPT_HEADER, 1);
		curl_setopt($curl, CURLOPT_HTTPGET, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);            
		$response = curl_exec($curl);
		$http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		$headers = substr($response, 0, $header_size);
		$body = substr($response, $header_size);
		self::http_parse_headers($headers);
		curl_close ($curl);
		if ($http_status == 200) {
			$results = json_decode($body, true);
			return $results;
		} else {
			$this->error($body, $url, null, 'GET');
		} 
	
	}
	/**
	 * Performs a put request to the instantiated class
	 * 
	 * Accepts the resource to perform the request on, and fields to be sent
	 * 
	 * @param string $resource a string to perform get on
	 * @param array $fields an array to be sent in the request
	 * @return results or var_dump error
	 */
	public function put($resource, $fields) {
			
		$url = $this->_path . '/api/v2' . $resource;
		$json = json_encode($fields);
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $this->_headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_VERBOSE, 1);
		curl_setopt($curl, CURLOPT_HEADER, 1);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($curl, CURLOPT_POSTFIELDS, $json); 
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$response = curl_exec($curl);
		$http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		$headers = substr($response, 0, $header_size);
		$body = substr($response, $header_size);
		self::http_parse_headers($headers);
		curl_close($curl);
		if ($http_status == 200) {
			$results = json_decode($body, true);
			return $results;
		} else {
			$this->error($body, $url, $json, 'PUT');
		}
	}
	/**
	 * Performs a post request to the instantiated class
	 * 
	 * Accepts the resource to perform the request on, and fields to be sent
	 * 
	 * @param string $resource a string to perform get on
	 * @param array $fields an array to be sent in the request
	 * @return results or var_dump error
	 */
	public function post($resource, $fields) {
		global $error;
		$url = $this->_path . '/api/v2' . $resource;
		$json = json_encode($fields);
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $this->_headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_VERBOSE, 1);
		curl_setopt($curl, CURLOPT_HEADER, 1);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$response = curl_exec ($curl);
		
		
		
		$http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		$headers = substr($response, 0, $header_size);
		$body = substr($response, $header_size);
		self::http_parse_headers($headers);
		curl_close ($curl);
		if ($http_status == 201) {
			$results = json_decode($body, true);
			
			
			return $results;
		} else {
		
		
		
			$this->error($body, $url, $json, 'POST');
		}
	}
	/**
	 * Performs a delete request to the instantiated class
	 * 
	 * Accepts the resource to perform the request on
	 * 
	 * @param string $resource a string to perform get on
	 * @return proper response or var_dump error
	 */
	public function delete($resource) {
			
		$url = $this->_path . '/api/v2' . $resource;
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $this->_headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_VERBOSE, 1);
		curl_setopt($curl, CURLOPT_HEADER, 1);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$response = curl_exec($curl);
		$http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		$headers = substr($response, 0, $header_size);
		$body = substr($response, $header_size);
		self::http_parse_headers($headers);	        
		curl_close ($curl);
		if ($http_status == 204) {
	     	return $http_status . ' DELETED';
		 } else {
		 	$this->error($body, $url, null, 'DELETE');
		 }
	}
}
?>