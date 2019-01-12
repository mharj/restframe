<?php
namespace mharj;

class JsonIOFactory extends IOFactory {
	public function fromString(string $string) {
		return json_decode($string);
	}
	public function toString($object): string {
		return json_encode($object);
	}
	public function getContentType(): string {
		return 'application/json';
	}
}
