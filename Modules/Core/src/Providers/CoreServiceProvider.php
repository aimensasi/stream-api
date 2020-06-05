<?php

namespace Modules\Core\Providers;

use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider{
	
  /**
   * Boot the application events.
   *
   * @return void
   */
  public function boot(){
    $this->registerConfig();
    $this->registerCommands();
  }

  /**
   * Register the service provider.
   *
   * @return void
   */
  public function register(){

  }

  /**
   * Register Console commands 
   * 
   * @return void
   */
  public function registerCommands()
  {
    $this->commands([
      \Modules\Core\Console\RunTests::class,
    ]);
  }

  /**
   * Register config.
   *
   * @return void
   */
  protected function registerConfig(){
    $this->publishes([
      module_path('Core', 'src/Config/config.php') => config_path('core.php'),
    ], 'config');
    $this->mergeConfigFrom(
      module_path('Core', 'src/Config/config.php'), 'core'
    );
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
