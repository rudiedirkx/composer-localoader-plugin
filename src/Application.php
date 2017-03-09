<?php

namespace rdx\localoader;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use rdx\localoader\Localoader;
use rdx\localoader\commands\AddCommand;
use rdx\localoader\commands\AliasCommand;
use rdx\localoader\commands\ListCommand;

class Application extends BaseApplication {

	protected $localoader;

	/**
	 *
	 */
	public function __construct(Localoader $localoader) {
		$this->localoader = $localoader;

		parent::__construct('LOCALOADER', '1.0');
	}

	/**
	 *
	 */
	protected function getDefaultCommands() {
		return [
			new ListCommand(),
			new AddCommand(),
			new AliasCommand(),
		];
	}

	/**
	 *
	 */
	protected function getCommandName(InputInterface $input) {
		$name = parent::getCommandName($input);
		return $name ?: 'list';
	}

	/**
	 *
	 */
	public function getLocaloader() {
		return $this->localoader;
	}

}
