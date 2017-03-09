<?php

namespace rdx\localoader\commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends Command {

	/**
	 *
	 */
	protected function configure() {
		$this->setName('list');
		$this->setDescription('List the current localoads.');
	}

	/**
	 *
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$localoader = $this->getApplication()->getLocaloader();

		$output->writeLn(print_r($localoader->getLocaLoads(), 1));
	}

}
