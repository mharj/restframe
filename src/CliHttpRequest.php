<?php
namespace mharj;

class CliHttpRequest extends HttpRequest {
  public function __construct() {
    $this->headers = array(); // todo: get from argv
  }
}
