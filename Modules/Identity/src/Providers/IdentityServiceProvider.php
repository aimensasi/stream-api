<?php

namespace Modules\Identity\Providers;

use Laravel\Passport\Passport;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class IdentityServiceProvider extends ServiceProvider{
	
  /**
   * Boot the application events.
   *
   * @return void
   */
  public function boot(){
    Passport::tokensExpireIn(now()->addDays(1));
    Passport::refreshTokensExpireIn(now()->addDays(30));

    $this->registerConfig();
    $this->registerFactories();
    $this->loadMigrationsFrom(module_path('Identity', 'src/Database/Migrations'));
  }

  /**
   * Set the domain of the application based on the env variable
   *
   * @return void
   */
  private function domain(string $subdomain = ''){
    if (strlen($subdomain) > 0) {
      $subdomain = "{$subdomain}.";
    }

    return config('app.domain_prefix') . $subdomain . config('app.domain');
  }

  /**
   * Register the service provider.
   *
   * @return void
   */
  public function register(){

  }

  /**
   * Register config.
   *
   * @return void
   */
  protected function registerConfig(){
    $this->publishes([
      module_path('Identity', 'src/Config/config.php') => config_path('identity.php'),
    ], 'config');
    $this->mergeConfigFrom(
      module_path('Identity', 'src/Config/config.php'), 'identity'
    );
  }


  /**
   * Register translations.
   *
   * @return void
   */
  public function registerTranslations(){
    $langPath = resource_path('lang/modules/identity');

    if (is_dir($langPath)) {
      $this->loadTranslationsFrom($langPath, 'identity');
    } else {
      $this->loadTranslationsFrom(module_path('Identity', 'src/Resources/lang'), 'identity');
    }
  }

  /**
   * Register an additional directory of factories.
   *
   * @return void
   */
  public function registerFactories(){
    if (! app()->environment('production') && $this->app->runningInConsole()) {
      app(Factory::class)->load(module_path('Identity', 'src/Database/factories'));
    }
  }

  /**
   * Get the services provided by the provider.
   *
   * @return array
   */
  public function provides(){
    return [];
  }
}
