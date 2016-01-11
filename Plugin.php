<?php

namespace rdx\localoader;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\ScriptEvents;
use Composer\Script\Event;
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
			ScriptEvents::POST_AUTOLOAD_DUMP => 'onPostAutoloadDump',
		);
	}

	/**
	 *
	 */
	public function onPostAutoloadDump(Event $event) {
		$vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');
var_dump($vendorDir);

		rename($vendorDir . '/autoload.php', $vendorDir . '/autoload-composer.php');
		copy(__DIR__ . '/autoload-dev.php', $vendorDir . '/autoload.php');
	}

	/**
	 *
	 */
	static public function localoadCommand(Event $event) {
		var_dump(get_class($event));
	}

}
