<?php
namespace mharj;

class HttpRequest {
  private $headers = array();
  protected function __construct() {
    $this->headers = getallheaders();
  }
  
  protected function getHeaderNames(): array {
    return array_keys($this->headers);
  }
  
  protected function getHeader(string $name) {
    if ( isset($this->headers[$name]) ) {
      return $this->headers[$name];
    }
    return null;
  }
  
  public static function getServerHttpRequest() {
    if ( defined('HHVM_VERSION') ) {
      return new HhvmHttpRequest();
    }
    if( strpos( $_SERVER['SERVER_SOFTWARE'], 'Apache') !== false) {
      return new ApacheHttpRequest();
    } 
    return new CommonHttpRequest();
  }
}
