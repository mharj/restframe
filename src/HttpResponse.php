<?php
namespace mharj;

class HttpResponse {
  private $headers = array();
  public function addHeader(string $name, string $value) {
    $this->headers[$name]=$value;
  }
  public function getHeaderNames(): array {
    return array_keys($headers);
  }
  public function getHeader(string $name) {
    if ( isset($this->headers[$name]) ) {
      return $this->headers[$name];
    }
    return null;
  }
}
