<?php
namespace mharj;

class HttpResponse {
	private $headers = array();
	private $status = 200;
	private $is_raw = false;
	private $content = null;
	private $compress = false;
  
	/**
	 *  Add value end of array or create new one if not existing
	 */
	public function addHeader(string $name, string $value) {
		if (! isset($this->headers[$name]) ) {
			$this->headers[$name]=array($value);
		} else {
			$this->headers[$name][]=$value;
		}
	}
  
	/**
	 * Set value and override all existing ones in array
	 */
	public function setHeader(string $name, string $value) {
		$this->headers[$name]=array($value);
	}

	public function getHeaderNames(): array {
		return array_keys($this->headers);
	}
  
	public function getHeader(string $name) {
		if ( isset($this->headers[$name]) ) {
			return $this->headers[$name];
		}
		return null;
	}

	public function setCompression($status=false) {
		$this->compress = $status;
	}
	
	public function getCompression(): bool {
		return $this->compress;
	}
	
	public function setStatus(int $status) {
		$this->status = $status;
	}
	public function getStatus(): int {
		return $this->status;
	}
	
	public function setRaw(bool $raw) {
		$this->is_raw = $raw;
	}
	
	public function isRaw(): bool {
		return $this->is_raw;
	}
	
	public function setContent($content) {
		$this->content = $content;
	}
	
	public function getContent() {
		return $this->content;
	}
}
