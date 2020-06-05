<?php

namespace Modules\Core\Helpers;

use Modules\Core\Helpers\Common;

class Response {

	public const CODE_200 = 200;
	public const CODE_201 = 201;
	public const CODE_401 = 401;
	public const CODE_403 = 403;
	public const CODE_404 = 404;
	public const CODE_400 = 400;
	public const CODE_422 = 422;
	public const CODE_500 = 500;
	public const CODE_900 = 900;
	public const CODE_901 = 901;
	

	public static function create($data, $status_code = self::CODE_200, $headers = []){
		return response()->json($data, $status_code, $headers);
	}

	public static function unauthorized($message = null, $status_code = self::CODE_401){
		$message = $message ?? ["message" => "You don't have authorization to access this resource."];
		return self::create($message, $status_code);
	}


	public static function forbidden($message = null, $status_code = self::CODE_403){
		$message = $message ?? ["message" => "You don't have permission to access this resource."];
		return self::create($message, $status_code);
	}


	public static function badRequest($message = null, $status_code = self::CODE_400){
		$message = $message ?? ["message" => "Something does not look right with this request, please try again later."];
		return self::create($message, $status_code);
	}


	public static function validationError($message = null, $status_code = self::CODE_422){
		$message = $message ?? [
			"message" => "Something does not look right with this request, make sure check your data."
		];

		return self::create($message, $status_code);
	}


	public static function emailNotVerified($message = null, $extra = null){
		$message = $message ?? "Please verify your email to be able to access this resource";

		$data = [
			"message" => $message,
			"code" => self::CODE_900,
			"extra" => $extra
		];

		return self::create($data, self::CODE_422);
	}

	public static function phoneNotVerified($message = null, $extra = null){
		$message = $message ?? "Please verify your phone number to be able to access this resource";

		$data = [
			"message" => $message,
			"code" => self::CODE_901,
			"extra" => $extra
		];

		return self::create($data, self::CODE_422);
	}

	public static function serverError($message = null, $status_code = self::CODE_500){
		$message = $message ?? [
			"message" => "Something went wrong on our side, please try again later or contact support."
		];

		return self::create($message, $status_code);
	}


	public static function notFound($message = ["message" => "Not Found"], $status_code = self::CODE_404){
		$message = $message ?? ["message" => "The resource you are looking for could not be found."];
		return self::create($message, $status_code);
	}



	public static function success($message = null, $status_code = self::CODE_200){
		$message = $message ?? ["message" => "OK"];
		return self::create($message, $status_code);
	}



	public static function created($message = null, $status_code = self::CODE_201){
		$message = $message ?? ["message" => "Accepted!"];
		return self::create($message, $status_code);
	}
}	