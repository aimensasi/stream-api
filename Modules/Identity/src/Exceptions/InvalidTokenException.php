<?php

namespace Modules\Identity\Exceptions;

use Exception;

class InvalidTokenException extends Exception{

	// Redefine the exception so message isn't optional
	public function __construct($message = null, $code = 0, Exception $previous = null){
		$message = $message ?? 'The given code/token is either invalid or expired.';
		// make sure everything is assigned properly
		parent::__construct($message, $code, $previous);
	}
}
