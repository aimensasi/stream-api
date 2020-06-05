<?php

namespace Modules\Core\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Process\Process;

class RunTests extends Command{
  
  /**
   * The console command name.
   *
   * @var string
   */
  protected $signature = 'run:test 
    {name? : The name of the module to run tests forart .}
    {--g|group= : The group to run tests for }';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Run Phpunit tests for the specified module.';

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct(){
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle(){
    $name = $this->argument('name');
    $group = $this->option('group');

    if ($name) {
      $this->runFor($name, $group);

      return;
    }

    /** @var \Nwidart\Modules\Module $module */
    foreach ($this->laravel['modules']->getOrdered() as $module) {
      $this->runFor($module->getName(), $group);
    }
  }

  public function runFor($name, $group){

    $this->line('Running for module: <info>' . $name . '</info>');

    $process = new Process([
      base_path('vendor/bin/phpunit'),
      'Modules/' . $name,
      $group != null ? '--group=' . $group : ''
    ]);

    $process->setTty(true);
    $process->run();

    echo $process->getOutput();
  }

  /**
   * Get the console command arguments.
   *
   * @return array
   */
  protected function getArguments(){
    return [
      ['example', InputArgument::REQUIRED, 'An example argument.'],
    ];
  }

  /**
   * Get the console command options.
   *
   * @return array
   */
  protected function getOptions(){
    return [
      ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
    ];
  }
}
