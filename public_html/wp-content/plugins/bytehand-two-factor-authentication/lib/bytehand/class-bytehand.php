<?php
/**
* bytehand PHP API
*
* @package     bytehand
* @copyright   bytehand Ltd 2015
* @license     ISC
* @link        http://www.bytehand.com
* @version     1.3.0
*/

if ( !class_exists('bytehandException') ) {
  require_once('class-bytehandException.php');
}

/**
* Main bytehand API Class
* 
* @package     bytehand
* @since       1.0
*/
class bytehand {

  /**
  * All bytehand API calls start with BASE_URL
  */
  const API_BASE_URL      = 'http://api.bytehand.com/send';

  /**
  * Use SSL when making HTTP requests
  *
  * If this is not set, SSL will be used where PHP supports it
  *
  * @var bool
  */
  public $ssl;

  public $id;
  public $key;

 
  /**
  * From address used on text messages
  */
  public $from;


  /**
  * Create a new instance of the bytehand wrapper
  *
  * @param   string  key         Your bytehand API Key
  * @param   array   options     Optional parameters for sending SMS
  */
  public function __construct($id, $key, array $options = array()) {
    if (empty($key) || empty($id)) {
      throw new bytehandException("Id/Key can't be blank");
    } else {
      $this->key = $key;
	  $this->id = $id;
    }    
	$this->from = (array_key_exists('from', $options)) ? $options['from'] : null;
 }

  /**
  * Send some text messages
  * 
  */
  public function send(array $data) {
	
	foreach ($data as $key => $sms) {
		if (!$sms['from']) {
			$sms['from'] = $this->from;
		}
		$answer = @file_get_contents
			(
				'http://api.bytehand.com/send?id=' . $this->id . '&key=' 
				. $this->key . '&to=' . urlencode($sms['to']) . '&from=' . urlencode($sms['from']) 
				. '&text='.urlencode($sms['message'])
			);
		
		if ($answer){ 
			$json = json_decode( $answer );
			if ($json->status == 0) {
				$result[ $key ]['success'] = '1';
				$result[ $key ]['id'] = $json->description;
			}
		}else {
			$result[ $key ]['error_code'] = '1';
			$result[ $key ]['error_message'] = 'Problem with sending message from ' . $sms['from'] . ' to ' . $sms['to'];
			$result[ $key ]['success'] = '0';
		}
	}
	return $result;
  }

 
  /**
  * Check your account balance
  *
  * @return  array   Array of account balance: 
  */
  public function checkBalance() {
	   $result = @file_get_contents
			(
				'http://api.bytehand.com/balance?id=' . $this->id . '&key=' 
				. $this->key
			);
	if ($result === false) {
        return false;
    }else {
		$json = json_decode( $result );
	}
	
	if ($json) {
		$balance = round($json->description, 1);
	}else {
		$balance = false;
	}
	//$balance = 0;
    return array( 'balance' => $balance );
  }

  /**
  * Check whether the API Key is valid
  *
  * @return  bool    True indicates a valid key
  */
  public function checkKey() {
    return true;  
  }

  /**
  * Make an HTTP POST to bytehand
  *
  * @param   string   method bytehand method to call (sms/credit)
  * @param   string   data   Content of HTTP POST
  *
  * @return  string          Response from bytehand
  */
  protected function postTobytehand($method, $data) {
    if ($this->log) {
      $this->logXML("API $method Request XML", $data);
    }
    
    if( isset( $this->ssl ) ) {
      $ssl = $this->ssl;
    } else {
      $ssl = $this->sslSupport();
    }

    $url = $ssl ? 'https://' : 'http://';
    $url .= self::API_BASE_URL . $method;

    $response = $this->xmlPost($url, $data);

    if ($this->log) {
      $this->logXML("API $method Response XML", $response);
    }

    return $response;
  }

  /**
  * Make a HTTP POST
  *
  * cURL will be used if available, otherwise tries the PHP stream functions
  *
  * @param   string url      URL to send to
  * @param   string data     Data to POST
  * @return  string          Response returned by server
  */
  protected function xmlPost($url, $data) {
    if(extension_loaded('curl')) {
      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
      curl_setopt($ch, CURLOPT_USERAGENT, 'bytehand PHP Wrapper/1.0' . self::VERSION);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
      if (isset($this->proxy_host) && isset($this->proxy_port)) {
        curl_setopt($ch, CURLOPT_PROXY, $this->proxy_host);
        curl_setopt($ch, CURLOPT_PROXYPORT, $this->proxy_port);
      }

      $response = curl_exec($ch);
      $info = curl_getinfo($ch);

      if ($response === false || $info['http_code'] != 200) {
        throw new Exception('HTTP Error calling bytehand API - HTTP Status: ' . $info['http_code'] . ' - cURL Erorr: ' . curl_error($ch));
      } elseif (curl_errno($ch) > 0) {
        throw new Exception('HTTP Error calling bytehand API - cURL Error: ' . curl_error($ch));
      }

      curl_close($ch);

      return $response;
    } elseif (function_exists('stream_get_contents')) {
      // Enable error Track Errors
      $track = ini_get('track_errors');
      ini_set('track_errors',true);

      $params = array('http' => array(
      'method'  => 'POST',
      'header'  => "Content-Type: text/xml\r\nUser-Agent: mediaburst PHP Wrapper/" . self::VERSION . "\r\n",
      'content' => $data
      ));

      if (isset($this->proxy_host) && isset($this->proxy_port)) {
        $params['http']['proxy'] = 'tcp://'.$this->proxy_host . ':' . $this->proxy_port;
        $params['http']['request_fulluri'] = True;
      }

      $ctx = stream_context_create($params);
      $fp = @fopen($url, 'rb', false, $ctx);
      if (!$fp) {
        ini_set('track_errors',$track);
        throw new Exception("HTTP Error calling bytehand API - fopen Error: $php_errormsg");
      }
      $response = @stream_get_contents($fp);
      if ($response === false) {
        ini_set('track_errors',$track);
        throw new Exception("HTTP Error calling bytehand API - stream Error: $php_errormsg");
      }
      ini_set('track_errors',$track);
      return $response;
    } else {
      throw new Exception("bytehand requires PHP5 with cURL or HTTP stream support");
    }
  }

  /**
  * Does the server/HTTP wrapper support SSL
  *
  * This is a best guess effort, some servers have weird setups where even
  * though cURL is compiled with SSL support is still fails to make
  * any requests.
  *
  * @return bool     True if SSL is supported
  */
  protected function sslSupport() {
    $ssl = false;
    // See if PHP is compiled with cURL
    if (extension_loaded('curl')) {
      $version = curl_version();
      $ssl = ($version['features'] & CURL_VERSION_SSL) ? true : false;
    } elseif (extension_loaded('openssl')) {
      $ssl = true;
    }
    return $ssl;
  }

  /**
  * Log some XML, tidily if possible, in the PHP error log
  *
  * @param   string  log_msg The log message to prepend to the XML
  * @param   string  xml     An XML formatted string
  *
  * @return  void
  */
  protected function logXML($log_msg, $xml) {
    // Tidy if possible
    if (class_exists('tidy')) {
      $tidy = new tidy;
      $config = array(
      'indent'     => true,
      'input-xml'  => true,
      'output-xml' => true,
      'wrap'       => 200
      );
      $tidy->parseString($xml, $config, 'utf8');
      $tidy->cleanRepair();
      $xml = $tidy;
    }
    // Output
    error_log("bytehand $log_msg: $xml");
  }

  /**
  * Check if an array is associative
  *
  * @param   array $array Array to check
  * @return  bool
  */
  protected function is_assoc($array) {
    return (bool)count(array_filter(array_keys($array), 'is_string'));
  }
  
  /**
   * Check if a number is a valid MSISDN
   *
   * @param string $val Value to check
   * @return bool True if valid MSISDN
   * @author James Inman
   * @since 1.3.0
   * @todo Take an optional country code and check that the number starts with it
   */
  public static function is_valid_msisdn($val) {
    return preg_match( '/^[1-9][0-9]{10,14}$/', $val );
  }

}
