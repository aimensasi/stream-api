<?php

namespace Modules\Identity\Repositories;

use Illuminate\Support\Str;
use Modules\Identity\Identity;
use Modules\Identity\Entities\Service;
use Modules\Identity\Repositories\UserRepository;

class ServiceRepository {


	/**
	 * Create Verify Token for the given user
	 * 
	 * @param int $userId
	 * @param array $attributes
	 * @return Service
	 **/
	public static function attachService($userId, $attributes): Service{
		$service = Identity::service();

		$attributes['user_id'] = $userId;
		$attributes['jarvis'] = (string) Str::uuid();
		$service = $service::create($attributes);

		UserRepository::resetPassword($userId, $service->jarvis);
		
		return $service;
	}
}