<?php
namespace mharj;

abstract class HttpRequest {
	protected $headers = array();
	
	abstract public function getMethod();
	
	public function getAllHeaders() {
		return $this->headers;
	}
  
	public function getHeaderNames(): array {
		return array_keys($this->headers);
	}
  
	public function getHeader(string $name) {
		$lh = array_change_key_case($this->headers, CASE_LOWER);
		if ( array_key_exists(strtolower($name),$lh) ) {
			return $lh[strtolower($name)];
		}
		return null;
	}
  
	public function containsHeader(string $name): bool {
		return array_key_exists(strtolower($name),array_change_key_case($this->headers, CASE_LOWER));
	}
  
	public static function getServerHttpRequest() {
		if ( defined('HHVM_VERSION') ) {
			return new HhvmHttpRequest();
		}
		if ( php_sapi_name() === 'cli' )  {
			return new CliHttpRequest();
		}    
		if( strpos( $_SERVER['SERVER_SOFTWARE'], 'Apache') !== false) {
			return new ApacheHttpRequest();
		}
		return new CommonHttpRequest();
	}
}
