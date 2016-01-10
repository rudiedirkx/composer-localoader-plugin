<?php

namespace rdx\localoader;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Plugin\PluginEvents;
use Composer\EventDispatcher\EventSubscriberInterface;

class Plugin implements PluginInterface, EventSubscriberInterface {

	protected $composer;
	protected $io;

	/**
	 * Dependency constructor
	 */
	public function activate(Composer $composer, IOInterface $io) {
		$this->composer = $composer;
		$this->io = $io;
	}

	/**
	 *
	 */
	public static function getSubscribedEvents() {
		return array(
			PluginEvents::PRE_AUTOLOAD_DUMP => 'onPreAutoloadDump',
			PluginEvents::POST_AUTOLOAD_DUMP => 'onPostAutoloadDump',
		);
	}

	/**
	 *
	 */
	public function onPreAutoloadDump($event) {

		echo __METHOD__ . "\n";
		print_r($event);
		exit;

	}

	/**
	 *
	 */
	public function onPostAutoloadDump($event) {

		echo __METHOD__ . "\n";
		print_r($event);
		exit;

	}

}
