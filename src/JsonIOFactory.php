<?php
namespace mharj;

class JsonIOFactory extends IOFactory {
	public function fromString($string) {
		return json_decode($string);
	}
	public function toString($object) {
		if ( is_null($object) ) {
			return "";
		}
		return json_encode($object);
	}
	public function getContentType() {
		return 'application/json';
	}
}
