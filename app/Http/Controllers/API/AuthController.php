<?php

namespace App\Http\Controllers\API;

use Modules\Identity\Identity;
use Modules\Identity\Exceptions\InvalidTokenException;

use Modules\Core\Helpers\Response;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller{
    

    /**
	 * Register the user
	 * 
	 * 
	 * @param Request $request
	 * @return \Illuminate\Http\Response
	 **/
	public static function register(Request $request){
		$validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
		]);

		if ($validator->fails()) {
			return Response::validationError(['message' => $validator->errors()->first()]);
		}

		$data = $validator->validated();
		$roleId = Identity::getRoleIdForUser();

		$data = [
			'email' => $data['email'],
			'password' => $data['password'],
		];

		try{

			$user = Identity::createUser($data, $roleId);

            $data['user_id'] = $user->id;
            $data['client_id'] = $request->header('client-id');
            $data['client_secret'] = $request->header('client-secret');

            $result = Identity::createAccessToken($data);
            $result['id'] = $user->id;
            $result['email'] = $user->email;

            return Response::create($result);
            
		}catch(\Exception $e){
			return Response::badRequest([
				'message' => "Something went wrong with your last request.",
				"description" => $e->getMessage(),
			]);
		}
    }

    /**
     * Login the user
     * 
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     **/
    public static function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|exists:users',
            'password' => 'required|min:6',
        ]);


        if ($validator->fails()) {
            return Response::validationError(['message' => $validator->errors()->first()]);
        }

        $data = $validator->validated();
        $roleId = Identity::getRoleIdForUser();

        $user = Identity::findUserByEmailAndRoleId($data['email'], $roleId);

        if (!$user) {
            return Response::validationError([
                'message' => 'Email or password is incorrect',
            ]);
        }

        if (!Hash::check($data['password'], $user->password)) {
            return Response::validationError([
                'message' => 'Email or password is incorrect',
            ]);
        }


        $data['user_id'] = $user->id;
        $data['client_id'] = $request->header('client-id');
        $data['client_secret'] = $request->header('client-secret');

        $result = Identity::createAccessToken($data);
        $result['id'] = $user->id;
        $result['email'] = $user->email;

        return Response::create($result);
    }
}
