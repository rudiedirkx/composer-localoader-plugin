<?php

namespace rdx\localoader;

use Exception;
use InvalidArgumentException;
use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\ScriptEvents;
use Composer\Script\Event;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Json\JsonFile;

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
			ScriptEvents::POST_AUTOLOAD_DUMP => 'onPostAutoloadDump',
		);
	}

	/**
	 *
	 */
	public function onPostAutoloadDump(Event $event) {
		$vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');

		rename($vendorDir . '/autoload.php', $vendorDir . '/autoload-composer.php');
		copy(__DIR__ . '/autoload-dev.php', $vendorDir . '/autoload.php');
	}

	/**
	 *
	 */
	static public function localoadCommand(Event $event) {
		$vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');
		$rootDir = dirname($vendorDir);

		$args = $event->getArguments();
		if (count($args) < 2) {
			throw new InvalidArgumentException('This command requires 2 arguments: namespace location');
		}

		$localoadJson = new JsonFile($rootDir . '/composer-locaload.json');
		try {
			$locaload = $localoadJson->read();
		}
		catch (Exception $ex) {
			$locaload = array();
		}

		$locaload[ $args[0] ] = $args[1];
		$localoadJson->write($locaload);
	}

}
