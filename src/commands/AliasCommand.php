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
	protected function execute(InputInterface $input, OutputInterface $output) : int {
		$localoader = $this->getApplication()->getLocaloader();

		$args = $input->getArguments();

		if (!$localoader->validatePackage($args['package'])) {
			$output->writeLn(sprintf("ERROR: Invalid package '%s'. You must `require` it first.", $args['package']));
			return 1;
		}

		if (!$localoader->validateSource($args['source'])) {
			$output->writeLn(sprintf("ERROR: Invalid source '%s'. It must be an existing package directory.", $args['source']));
			return 1;
		}

		$localoader->addPackageException($args['package'], $args['source']);

		$output->writeLn(print_r($localoader->getLocaLoads(), 1));

		// @todo Call this the 'proper' way?
		`composer dumpautoload`;

		return 0;
	}

}
