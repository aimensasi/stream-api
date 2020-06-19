<?php

namespace App\Console\Commands\MakeCommands;

use Illuminate\Foundation\Console\ModelMakeCommand as Command;

class ModelMakeCommand extends Command{

	/**
   * Get the default namespace for the class.
   *
   * @param  string  $rootNamespace
   * @return string
   */
  protected function getDefaultNamespace($rootNamespace){
    return "{$rootNamespace}\Models";
  }

	protected function getStub(){
		if ($this->option('pivot')) {
			return __DIR__ . '/stubs/models/pivot.model.stub';
		}

		return __DIR__ . '/stubs/models/model.stub';
	}
}
