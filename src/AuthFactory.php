<?php
namespace mharj;
/**
 * Common abstract Authentication class for all implementations
 */
abstract class AuthFactory {
	/**
	 * check if authenticated
	 * @return bool
	 */
	abstract public function check();
}
