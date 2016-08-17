<?php
namespace mharj;

class CliHttpRequest extends HttpRequest {
	public function __construct() {
		if ( ! isset($argv) ) {
			$argv=array();
		}
		$this->headers = array_merge($argv,$_ENV);
	}
  
	public function getMethod() {
		return (isset($_ENV['REQUEST_METHOD'])?$_ENV['REQUEST_METHOD']:"GET");
	}
}
