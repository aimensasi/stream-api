<?php

namespace Modules\Identity\Tests\Unit;

use Modules\Identity\Identity;
use Modules\Identity\Exceptions\UnSopportedRoleException;
use Modules\Identity\Exceptions\InvalidTokenException;

use Tests\TestCase;
use Laravel\Passport\Client;
use Laravel\Passport\ClientRepository;

use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IdentityTest extends TestCase{
  use RefreshDatabase;

  /**
   * Supported Roles
   * 
   */
  protected $supportedRoles;

  /**
   * Setup the test environment.
   *
   * @return void
   */
  protected function setUp(): void{
    parent::setUp();
    
    $this->supportedRoles = config('identity.roles');
  }

  /**
   * Get Supported Roles
   * 
   * @param callable $callable
   * @return void
   */
  public function supportedRoles(callable $callable){
    foreach ($this->supportedRoles as $roleName) {
      $method = 'getRoleIdFor' . $roleName;
      $roleId = forward_static_call([Identity::class, $method], []);

      $callable($roleId);
    }
  }

  /**
   * Create Test user with role
   * 
   * @param string $state
   * @param int $roleId
   * @return \Illuminate\Database\Eloquent\Model
   */
  public function createUser($state = null, $roleId){
    if($state){
      $usersFactory = factory(Identity::userModel(), 10)
        ->states($state)
        ->create()
        ->each(function ($user) use ($roleId) {
          $user->roles()->attach($roleId);
        });
    }else{
      $usersFactory = factory(Identity::userModel(), 10)
        ->create()
        ->each(function ($user) use ($roleId) {
          $user->roles()->attach($roleId);
        });
    }

    return $usersFactory->random();
  }

  /**
   * Create passport client setup
   * 
   * @return Client
   **/
  public function createPassportClient(): Client{
    $clientRepository = new ClientRepository();
    $client = $clientRepository->create(null, "Password Grant Client", 'localhost', false, true);

    return $client;
  }


  /**
   * @test
   * 
   * @group identity
   * @return void
   */
  public function it_can_get_user_model_instance(){
    $user = Identity::user();

    $this->assertInstanceOf(Identity::userModel(), $user);
  }

  /**
   * @test
   * 
   * @group identity
   * @return void
   */
  public function it_can_get_role_model_instance(){
    $role = Identity::role();

    $this->assertInstanceOf(Identity::roleModel(), $role);
  }

  /**
   * @test
   * 
   * @group identity
   * @return void
   */
  public function it_can_get_verify_token_model_instance(){
    $verifyToken = Identity::verifyToken();

    $this->assertInstanceOf(Identity::verifyTokenModel(), $verifyToken);
  }


  /**
   * @test
   * 
   * @group identity
   * @return void
   */
  public function it_can_get_supported_roles(){
    foreach($this->supportedRoles as $roleName){
      $method = 'getRoleFor' . $roleName;
      $role = forward_static_call([Identity::class, $method], []);

      $this->assertInstanceOf(Identity::roleModel(), $role);
      $this->assertEquals($role->name, $roleName);
    }
  }

  /**
   * @test
   * 
   * @group identity
   * 
   * @return void
   */
  public function it_should_throw_exception_for_unsupported_roles(){
    $this->expectException(UnSopportedRoleException::class);

    Identity::getRoleForUnsupportedAttribute();
  }


  /**
   * @test
   * 
   * @group identity
   * @return void
   */
  public function it_can_get_supported_roles_ids(){
    $role = Identity::role();

    foreach ($this->supportedRoles as $roleName) {
      $method = 'getRoleIdFor' . $roleName;
      $roleId = forward_static_call([Identity::class, $method], []);

      $role = $role::where('name', $roleName)->first();
      $this->assertEquals($role->id, $roleId);
    }
  }


  /**
   * @test
   * 
   * @group identity
   * @return void
   */
  public function it_can_create_user(){
    $this->supportedRoles(function($roleId){
      $userFactory = factory(Identity::userModel())->make()->makeVisible('password');
      $user = Identity::createUser($userFactory->toArray(), $roleId, $userFactory['must_verify_email']);

      $this->assertInstanceOf(Identity::userModel(), $user);
    });
  }

  /**
   * @test
   * 
   * @group identity
   * @return void
   */
  public function it_can_get_user_by_email_and_role(){
    $this->supportedRoles(function ($roleId) {
      $user = $this->createUser(null, $roleId);

      $foundUser = Identity::findUserByEmailAndRoleId($user->email, $roleId);

      $this->assertEquals($user['email'], $foundUser->email);
    });
  }


  /**
   * @test 
   * 
   * @group identity
   * @group verify_token
   * @return void
   */
  public function it_should_create_email_verify_token_for_user(){
    $this->supportedRoles(function ($roleId) {
      $user = $this->createUser('must_verify_email', $roleId);
      $verifyToken = Identity::createVerifyTokenForEmail($user->id);

      $this->assertEquals($verifyToken->used_for, Identity::verifyTokenModel()::EMAIL);
    });
  }

  /**
   * @test 
   * 
   * @group identity
   * @group verify_token
   * @return void
   */
  public function it_should_create_password_reset_token_for_user(){
    $this->supportedRoles(function ($roleId) {
      $user = $this->createUser('must_verify_email', $roleId);

      $verifyToken = Identity::createVerifyTokenForPassword($user->id);

      $this->assertEquals($verifyToken->used_for, Identity::verifyTokenModel()::PASSWORD);
    });
  }

  /**
   * @test 
   * 
   * @group identity
   * @group verify_token
   * @return void
   */
  public function it_should_create_phone_number_verify_token_for_user(){
    $this->supportedRoles(function ($roleId) {
      $user = $this->createUser('must_verify_email', $roleId);
      $verifyToken = Identity::createVerifyTokenForPhoneNumber($user->id);

      $this->assertEquals($verifyToken->used_for, Identity::verifyTokenModel()::PHONE_NUMBER);
    });
  }

  /**
   * @test
   * 
   * @group identity
   * @group verify_account
   * @return void
   */
  public function it_should_verify_account(){
    $this->supportedRoles(function ($roleId) {
      $user = $this->createUser('must_verify_email', $roleId);

      $verifyToken = factory(Identity::verifyTokenModel())
        ->state('email')
        ->create(['user_id' => $user->id]);

      $this->assertEquals($user->is_email_verified, false);

      Identity::verifyAccount($verifyToken->token);

      $user = Identity::findUserByEmailAndRoleId($user->email, $roleId);

      $this->assertEquals($user->is_email_verified, true);
    });
  }


  /**
   * @test
   * 
   * @group identity
   * @group verify_account
   * @return void
   */
  public function it_should_throw_exception_for_verify_account(){
    $this->supportedRoles(function ($roleId) {
      $user = $this->createUser('must_verify_email', $roleId);

      $verifyToken = factory(Identity::verifyTokenModel())
        ->state('email')
        ->create(['user_id' => $user->id, 'expire_in' => now()->subDays(2)]);

      $this->assertEquals($user->is_email_verified, false);

      $this->expectException(InvalidTokenException::class);
      Identity::verifyAccount($verifyToken->token);
    });
  }


  /**
   * @test
   * 
   * @group identity
   * @group reset_password
   * @return void
   */
  public function it_should_reset_password(){
    $this->supportedRoles(function ($roleId) {
      $user = $this->createUser(null, $roleId);

      $verifyToken = factory(Identity::verifyTokenModel())
        ->states('password')
        ->create(['user_id' => $user->id]);

      Identity::resetPassword($verifyToken->token, 'secret2020');

      $user = Identity::findUserByEmailAndRoleId($user->email, $roleId);

      $this->assertTrue(Hash::check('secret2020', $user->password));
    });
  }

  /**
   * @test
   * 
   * @group identity
   * @group reset_password
   * @return void
   */
  public function it_should_throw_exception_for_reset_password(){
    $this->supportedRoles(function ($roleId) {
      $user = $this->createUser(null, $roleId);

      $verifyToken = factory(Identity::verifyTokenModel())
        ->states('password')
        ->create(['user_id' => $user->id, 'expire_in' => now()->subDays(2)]);

      $this->expectException(InvalidTokenException::class);
      Identity::resetPassword($verifyToken->token, 'secret2020');
    });
  }


  /**
   * @test
   * 
   * @group identity
   * @group verify_phone_number
   * @return void
   */
  public function it_should_verify_phone_number(){
    $this->supportedRoles(function ($roleId) {
      $user = $this->createUser('must_verify_phone', $roleId);

      $verifyToken = factory(Identity::verifyTokenModel())
        ->states('phone_number')
        ->create(['user_id' => $user->id]);

      $this->assertEquals($user->is_phone_verified, false);

      Identity::verifyPhoneNumber($verifyToken->token);

      $user = Identity::findUserByEmailAndRoleId($user->email, $roleId);

      $this->assertEquals($user->is_phone_verified, true);
    });
  }

  /**
   * @test
   * 
   * @group identity
   * @group verify_phone_number
   * @return void
   */
  public function it_should_throw_exception_for_verify_phone_number(){
    $this->supportedRoles(function ($roleId) {
      $user = $this->createUser('must_verify_phone', $roleId);

      $verifyToken = factory(Identity::verifyTokenModel())
        ->states('phone_number')
        ->create(['user_id' => $user->id, 'expire_in' => now()->subDays(2)]);

      $this->assertEquals($user->is_phone_verified, false);

      $this->expectException(InvalidTokenException::class);
      Identity::verifyPhoneNumber($verifyToken->token);
    });
  }

  /**
   * @test 
   * 
   * @group identity
   * @group verify_token
   * @return void
   */
  public function it_can_revoke_verify_tokens_for_user(){
    $this->supportedRoles(function ($roleId) {
      $user = $this->createUser('must_verify_email', $roleId);
      $tokens = factory(Identity::verifyTokenModel(), 10)->create(['user_id' => $user->id]);

      Identity::revokeVerifyTokensFor($user->id);

      $this->expectException(InvalidTokenException::class);
      $verifyToken = $tokens->random();
      Identity::verifyAccount($verifyToken->token);
    });
  }

  /**
   * @test 
   * 
   * @group identity
   * @group verify_token
   * @return void
   */
  public function it_can_revoke_verify_token(){
    $tokens = factory(Identity::verifyTokenModel(), 10)->create();

    $verifyToken = $tokens->random();

    Identity::revokeVerifyToken($verifyToken->token);

    $this->expectException(InvalidTokenException::class);
    Identity::verifyAccount($verifyToken->token);
  }

  /**
   * @test
   * 
   * @group identity
   * @group identity_passport
   * @return void
   */
  public function it_can_issue_an_access_token(){
    $client = $this->createPassportClient();
    $this->supportedRoles(function($roleId) use ($client){
      $user = $this->createUser(null, $roleId);

      $data = [
        'user_id' => $user->id,
        "client_id" => $client->id,
        "client_secret" => $client->secret,
        "email" => $user->email,
        "password" => 'secret',
      ];      
      
      $response = Identity::createAccessToken($data);

      $this->assertArrayHasKey('access_token', $response);
      $this->assertArrayHasKey('refresh_token', $response);
      $this->assertArrayHasKey('token_type', $response);
      $this->assertArrayHasKey('expires_in', $response);
    });
  }


  /**
   * @test
   * 
   * @group identity
   * @group identity_passport
   * @return void
   */
  public function it_can_issue_a_refresh_token(){
    $client = $this->createPassportClient();
    $this->supportedRoles(function($roleId) use ($client){
      $user = $this->createUser(null, $roleId);

      $data = [
        'user_id' => $user->id,
        "client_id" => $client->id,
        "client_secret" => $client->secret,
        "email" => $user->email,
        "password" => 'secret',
      ];

      $response = Identity::createAccessToken($data);
      $refresh_token = $response['refresh_token'];

      $data = [
        "client_id" => $client->id,
        "client_secret" => $client->secret,
        "refresh_token" => $refresh_token,
      ];      
      
      $response = Identity::refreshAccessToken($data);

      $this->assertArrayHasKey('access_token', $response);
      $this->assertArrayHasKey('refresh_token', $response);
      $this->assertArrayHasKey('token_type', $response);
      $this->assertArrayHasKey('expires_in', $response);
    });
  }

  /**
   * It tests the ability to add thrid party login
   * providers such as Facebook, Google
   * 
   * @test 
   * 
   * @group identity
   * @group services
   * @return void
   **/
  public function it_should_attach_service_to_user(){
    $this->supportedRoles(function ($roleId) {
      $user = $this->createUser(null, $roleId);

      $service = factory(Identity::serviceModel())->make()->toArray();

      Identity::attachUserServices($user->id, $service);

      $user = Identity::findUserByEmailAndRoleId($user->email, $roleId);

      $this->assertNotEquals($user->services->count(), 0);
    });
  }
}
