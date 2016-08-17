<?php
namespace mharj;

class CommonHttpRequest extends HttpRequest {
	
	public function __construct() {
		if ( function_exists("getallheaders") ) { 
			$this->headers = getallheaders();
		} else {
			$this->headers = $_ENV; // help unit testing
		}
	}
	
	public function getMethod() {
		return (isset($_SERVER['REQUEST_METHOD'])?$_SERVER['REQUEST_METHOD']:"GET");
	}	
}
