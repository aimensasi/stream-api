<?php

namespace App\Console\Commands\MakeCommands;

use Illuminate\Support\Str;
use Illuminate\Routing\Console\ControllerMakeCommand as Command;

class ControllerMakeCommand extends Command{

	/**
	 * Get the fully-qualified model class name.
	 *
	 * @param  string  $model
	 * @return string
	 *
	 * @throws \InvalidArgumentException
	 */
	protected function parseModel($model){
		if (preg_match('([^A-Za-z0-9_/\\\\])', $model)) {
			throw new InvalidArgumentException('Model name contains invalid characters.');
		}

		$model = trim(str_replace('/', '\\', $model), '\\');

		if (! Str::startsWith($model, $rootNamespace = $this->laravel->getNamespace())) {
			$model = $rootNamespace . 'Models\\' . $model;
		}

		return $model;
	}

	/**
	 * Get the stub file for the generator.
	 *
	 * @return string
	 */
	protected function getStub(){
		$stub = null;

		if ($this->option('parent')) {
			$stub = '/stubs/controllers/controller.nested.stub';
		} elseif ($this->option('model')) {
			$stub = '/stubs/controllers/controller.model.stub';
		} elseif ($this->option('invokable')) {
			$stub = '/stubs/controllers/controller.invokable.stub';
		} elseif ($this->option('resource')) {
			$stub = '/stubs/controllers/controller.stub';
		}

		if ($this->option('api') && is_null($stub)) {
			$stub = '/stubs/controllers/controller.api.stub';
		} elseif ($this->option('api') && ! is_null($stub) && ! $this->option('invokable')) {
			$stub = str_replace('.stub', '.api.stub', $stub);
		}

		$stub = $stub ?? '/stubs/controllers/controller.plain.stub';

		return __DIR__.$stub;
	}
}
