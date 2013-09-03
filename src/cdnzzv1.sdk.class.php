<?php
/*
 *
 *  CDNZZ PHP SDK 
 *
 * @version 0.1.0
 * @copyright 2013 cdnzz.com, Inc.  
 * @author lixiang@grid-safe.com
 */

/**
 * Set default date timezone.
 */
date_default_timezone_set('Asia/Shanghai');

/**
 * Set api path.
 */
if(!defined('CDNZZ_API_PATH'))
    define('CDNZZ_API_PATH', dirname(__FILE__));

require_once CDNZZ_API_PATH.DIRECTORY_SEPARATOR.'config.inc.php';
require_once CDNZZ_API_PATH.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR
    .'requestcore'.DIRECTORY_SEPARATOR.'requestcore.class.php';

define('CDNZZ_NAME','cdnzz-sdk-php');
define('CDNZZ_VERSION','0.1.0');
define('CDNZZ_BUILD','201308211542');
define('CDNZZ_AUTHOR', 'lixiang@grid-safe.com');

/**
 * Default CDNZZ Exception
 */
class CDNZZ_Exception extends Exception {}

//Check curl extension
if(function_exists('get_loaded_extensions')){
	$extensions = get_loaded_extensions();
	if($extensions){
		if(!in_array('curl', $extensions)){
			throw new CDNZZ_Exception('curl extension is needed.');
		}
	}else{
		throw new CDNZZ_Exception('not any extension loaded,please check php enviroment');
    }
}else{
	throw new CDNZZ_Exception('function get_loaded_extensions has been disabled,please check php config.');
}


/**
 * cdnzz php sdk
 *
 * Visit https://www.cdnzz.com for more information.
 *
 * @version 0.1.0
 * @copyright www.cdnzz.com
 * @link https://www.cdnzz.com
 * @link https://www.cdnzz.com/help/user_api 
 */
class CDNZZ{

    /**
     * The host being requested.
     */
    const DEFALUT_CDNZZ_HOST = 'www.cdnzz.com';

    /**
     * The name of this sdk.
     */
    const NAME = CDNZZ_NAME;

    /**
     * The building number of this sdk.
     */
    const BUILD = CDNZZ_BUILD;

    /**
     * The version of this sdk.
     */
    const VERSION = CDNZZ_VERSION;

    /**
     * The author of this sdk.
     */
    const AUTHOR = CDNZZ_AUTHOR;

    const CDNZZ_HOST = 'Host';

    const CDNZZ_DATE = 'Date';

    const CDNZZ_CONTENT = 'Content';

    const CDNZZ_CONTENT_TYPE = 'Content-Type';

    const CDNZZ_CONTENT_LENGTH = 'Content-Length';

    const CDNZZ_URL_USER = 'user';

    const CDNZZ_URL_SIGNATURE = 'signature';

    /**
     * The host being requested.
     */
    public $hostname = '';

    /**
     * The port being requested.
     */
    public $port = '80';

    /**
     * The state of SSL/HTTPS use. 
     */
    public $use_ssl = FALSE;
    
    /**
     * The state of the debug mode setting.
     */
    public $debug_mode = FALSE;

    /**
     * The username to use for the request.
     */
    private $_username = '';

    /**
     * The signature to use for the request.
     */
    private $_signature = '';


    function __construct($api_username=NULL, $api_signature=NULL, $hostname=NULL){
        
        if( $api_username==NULL && !defined('CDNZZ_USER') ){
            throw new CDNZZ_Exception('get account failed.');
        }

        if( $api_signature==NULL && !defined('CDNZZ_SIGNATURE') ){
            throw new CDNZZ_Exception('get api signature failed.');
        }

        if( $api_username){
            $this->_username= $api_username;
        }elseif( defined('CDNZZ_USER') ){
            $this->_username= CDNZZ_USER;
        }

        if( $api_signature ){
            $this->_signature = $api_signature;
        }elseif( defined('CDNZZ_SIGNATURE') ){
            $this->_signature = CDNZZ_SIGNATURE;
        }

        if( empty($this->_username) || empty($this->_signature) ){
            throw new CDNZZ_Exception('account or api signature empty.');
        }
        
        if( $hostname ){
            $this->hostname = $hostname;
        }else{
            $this->hostname = self::DEFALUT_CDNZZ_HOST; 
        }
    }

    /**
     * Authenticates a connection to cdnzz. Do not use directly unless implementing custom methods for
     * this class.
     *
     * @param array $options The options of the sdk request.
     * @return ResponseCore
     */
    public function authenticate($options=array()){
        
        if( !isset($options[self::CDNZZ_URL_USER])){
            $options[self::CDNZZ_URL_USER] = $this->_username;
        }
        
        if( !isset($options[self::CDNZZ_URL_SIGNATURE]) ){
            $options[self::CDNZZ_URL_SIGNATURE] = $this->_signature;
        }

        $scheme = $this->use_ssl ? 'https://' : 'http://';
        $hostname = $this->hostname;
        
        $headers = array(
            self::CDNZZ_HOST => $hostname,
            self::CDNZZ_CONTENT_TYPE => 'application/x-www-form-urlencoded',
            self::CDNZZ_DATE => gmdate('D, d M Y H:i:s \G\M\T'),
        );
        
        if( isset($options['url_path']) ){
            $path = $options['url_path']; 
        }
        
        $request_url = $scheme.$hostname.$path;
        $request = new RequestCore($request_url);
        //$request->debug_mode=true;
        if(isset($options['api_method'])){
            $request->set_method($options['api_method']);
        }
        
        $request_content = self::CDNZZ_URL_USER.'='.$options[self::CDNZZ_URL_USER].'&'
            .self::CDNZZ_URL_SIGNATURE.'='.$options[self::CDNZZ_URL_SIGNATURE];
        if( isset($options['content']) ){
            foreach( $options['content'] as $content_key=>$content_value){
                $request_content .= '&'.$content_key.'='.urlencode($content_value);
            }
        }
        $request->set_body($request_content);
        $headers[self::CDNZZ_CONTENT_LENGTH] = strlen($request_content);
        
        foreach( $headers as $header_key=>$header_value){
			$header_value = str_replace ( array ("\r", "\n" ), '', $header_value );
            $request->add_header($header_key, $header_value);
        }
        
        $request->send_request();
        $response_header = $request->get_response_header();
        $response_body = $request->get_response_body();
		$data =  new ResponseCore ( $response_header , $request->get_response_body (), $request->get_response_code () );

        return $data;
    }


    /**
     * Purge cache from cdnzz.
     *
     * @param string $url(Required) The url that you want to purge cache.
     * @return ResponseCore
     *
     */
    public function purge_cache($url){
        if( !$this->validate_url($url) ){
            throw new CDNZZ_Exception('please input a valid url.');
        }

        if( !$options ){
            $options = array();
        }

        $options['url_path'] = '/api/json';
        $options['api_method'] = 'POST';
        $options['content'] = array(
            'method' => 'PurgeCache',
            'url' => $url, 
        );
        $this->use_ssl();
        $response = $this->authenticate($options);
        $response = (array)$response;
        if( !empty($response) ){
            $api_return = $response['body'];   
            $return_arr = json_decode($api_return, 1);
            if( $return_arr['result']=='success'){
                return 'success';
            }else{
                return $return_arr['msg'];
            }
        }else{
            return 'network error, try again.';
        }
    }

    /**
     * Preload resource from cdnzz.
     *
     * @param string $url(Required) The url that you want to preload.
     * @return ResponseCore
     * @link https://www.cdnzz.com/help/user_api
     */
    public function preload($url){
        if( !$this->validate_url($url) ){
            throw new CDNZZ_Exception('please input a valid url.');
        }

        if( !$options ){
            $options = array();
        }

        $options['url_path'] = '/api/json';
        $options['api_method'] = 'POST';
        $options['content'] = array(
            'method' => 'AddPreload',
            'url' => $url, 
        );
        $this->use_ssl();
        $response = $this->authenticate($options);
        $response = (array)$response;
        if( !empty($response) ){
            $api_return = $response['body'];   
            $return_arr = json_decode($api_return, 1);
            if( $return_arr['result']=='success'){
                return 'success';
            }else{
                return $return_arr['msg'];
            }
        }else{
            return 'network error, try again.';
        }
    }


    /**
     * Set hostname.
     * @param string $hostname
     * @param string $port
     *
     */
    private function set_hostname($hostname, $port=null){
        $this->hostname = $hostname;

        if( $port ){
            $this->port = $port;
            $this->hostname = $hostname.':'.$port;
        }
    }


    /**
     * Set the port number.
     * @param string $port
     *
     */
    private function set_port($port){
        $this->port = $port;
    }


    /**
     * Set ssl
     *
     * @param bool $use_ssl
     *
     */
    private function use_ssl($use_ssl=TRUE){
        $this->use_ssl = $use_ssl;
    }


    /**
     * Validate the url that will post to the host.
     *
     * @param string $url
     * @return bool
     */
    private function validate_url($url){
        if( ''==$url ){
            return false;
        }

        if( false==filter_var($url, FILTER_VALIDATE_URL) ){
            return false;
        }

        return true;
    }


}//end class
