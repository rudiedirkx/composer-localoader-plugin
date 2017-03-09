<?php

namespace rdx\localoader\commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use rdx\localoader\Localoader;

class AliasCommand extends Command {

	/**
	 *
	 */
	protected function configure() {
		$this->setName('alias');
		$this->setDescription('Alias an entire package to another location.');

		$this->addArgument('package', InputArgument::REQUIRED, 'Which Composer package to load locally.');
		$this->addArgument('source', InputArgument::REQUIRED, 'Where to load this package from.');
	}

	/**
	 *
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$localoader = $this->getApplication()->getLocaloader();

		$args = $input->getArguments();
		$package = $args['package'];
		$source = Localoader::encodeSource($args['source']);

		$localoader->addPackageException($package, $source);

		$output->writeLn(print_r($localoader->getLocaLoads(), 1));

		// @todo Call this the 'proper' way?
		`composer dumpautoload`;
	}

}
