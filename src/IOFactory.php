<?php
namespace mharj;
/**
 * abstract IO Factory for generic IO conversions
 */
abstract class IOFactory {
	/**
	 * string to object conversion
	 */
	abstract function fromString(string $string);
	/**
	 * object to string conversion
	 */ 
	abstract function toString($object): string;
	/**
	 * return IOFactory content type
	 */
	abstract function getContentType(): string;
}
