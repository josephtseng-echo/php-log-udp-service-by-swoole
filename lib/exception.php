<?php
/**
 * 
 * @author JosephZeng
 *
 */
class VerifyException extends Exception {
	public function __construct($message, $code = 1) {
		parent::__construct($message, $code);
	}
}