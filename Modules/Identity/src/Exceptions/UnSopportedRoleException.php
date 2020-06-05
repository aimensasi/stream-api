<?php

namespace Modules\Identity\Exceptions;

use Exception;

class UnSopportedRoleException extends Exception{

  // Redefine the exception so message isn't optional
  public function __construct($message = null, $code = 0, Exception $previous = null){
    $message = $message ?? 'The Given role is unsupported, make sure you have defined your roles in config/app.php.';
    // make sure everything is assigned properly
    parent::__construct($message, $code, $previous);
  }
    
}
