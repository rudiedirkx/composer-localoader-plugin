<?php

namespace rdx\localoader\commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use rdx\localoader\Application;
use rdx\localoader\Localoader;

class AddCommand extends Command {

	/**
	 *
	 */
	protected function configure() {
		$this->setName('add');
		$this->setDescription('Add an autoload exception.');

		$this->addArgument('namespace', InputArgument::REQUIRED, 'Which namespace to load locally.');
		$this->addArgument('source', InputArgument::REQUIRED, 'Where to load this namespace from.');

		$this->addOption('psr', null, InputOption::VALUE_REQUIRED, 'Which PSR to add this exception to: 0 or 4.', 4);
		$this->addOption('psr0', '0', InputOption::VALUE_NONE, 'Add this exception to PSR 0.');
		$this->addOption('psr4', '4', InputOption::VALUE_NONE, 'Add this exception to PSR 4.');
	}

	/**
	 *
	 */
	protected function getPSR(InputInterface $input) {
		$options = $input->getOptions();
		if ($options['psr0']) {
			return 0;
		}
		if ($options['psr4']) {
			return 4;
		}
		return $options['psr'];
	}

	/**
	 *
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$localoader = $this->getApplication()->getLocaloader();

		$psr = $this->getPSR($input);

		$args = $input->getArguments();
		$namespace = Localoader::encodeNamespace($args['namespace']);
		$source = Localoader::encodeSource($args['source']);

		if (strpos($namespace, '/') === false) {
			$output->writeLn("Namespace must contain at least 1 slash.");
			return 1;
		}

		if ($source[0] != '/') {
			$output->writeLn("Source must start with a slash.");
			return 2;
		}

		$localoader->addNamespaceException($psr, $namespace, $source);

		$output->writeLn(print_r($localoader->getLocaLoads(), 1));

		// @todo Call this the 'proper' way?
		`composer dumpautoload`;
	}

}
