<?php

namespace Modules\Identity\Repositories;

use Laravel\Passport\Passport;
use Laravel\Passport\RefreshTokenRepository;

use Psr\Http\Message\ServerRequestInterface;
use Laravel\Passport\Http\Controllers\AccessTokenController;

class PassportRepository {

	/**
	 * Create access token from attributes.
	 *
	 * @param array $attributes
	 * @return \Illuminate\Http\Response
	 */
	public static function createAccessToken($attributes){

		self::revokeAccessTokensFor($attributes['user_id'], $attributes['client_id']);

		$attributes['grant_type'] = 'password';
		$attributes['scope'] = '*';
		$attributes['username'] = $attributes['email'];

		$request = resolve(ServerRequestInterface::class);
		$request = $request->withParsedBody($attributes);

		$accessTokenController = resolve(AccessTokenController::class);
		$response = $accessTokenController->issueToken($request);
		

		if ($response->status() != 200) {
			return [
				"status" => $response->status(),
				"message" => "Email or password is incorrect",
				"description" => $response->content()
			];
		}

		return json_decode($response->content(), true);
	}

	/**
	 * Revoke Other Access Tokens for the specified user
	 * 
	 * 
	 * @param int $clientId
	 * @param int $userId
	 * @return void
	 */
	public static function revokeAccessTokensFor($userId, $clientId){
		$token = Passport::token();
		
		$token = $token->where('client_id', $clientId)->where('user_id', $userId)->where('revoked', false);

		$token->get()->map(function($token){
			(new RefreshTokenRepository)->revokeRefreshTokensByAccessTokenId($token->id);
		});

		$token->update(['revoked' => true]);
	}


	/**
	 * Refresh the user access token.
	 *
	 * @param array $attributes
	 * @return \Illuminate\Http\Response
	 */
	public static function refreshAccessToken($attributes){

		$attributes['grant_type'] = 'refresh_token';
		$attributes['scope'] = '*';

		$request = resolve(ServerRequestInterface::class);
		$request = $request->withParsedBody($attributes);

		$accessTokenController = resolve(AccessTokenController::class);
		$response = $accessTokenController->issueToken($request);


			if ($response->status() != 200) {
			return [
				"status" => $response->status(),
				"message" => "Email or password is incorrect",
				"description" => $response->content()
			];
		}

		return json_decode($response->content(), true);
	}

	/**
	 * Revoke User access.
	 *
	 * @param array $attributes
	 * @return void
	 */
	public static function revokeUserAccess($attributes){
		return self::revokeAccessTokensFor($attributes['user_id'], $attributes['client_id']);
	}

}