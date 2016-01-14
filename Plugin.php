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
		$config = $event->getComposer()->getConfig();
		$vendorDir = $config->get('vendor-dir');

		$global = $config->get('home') == dirname($vendorDir);

		if (!$global) {
			if (file_exists($from = __DIR__ . '/autoload-dev.php')) {
				rename($vendorDir . '/autoload.php', $vendorDir . '/autoload-composer.php');
				copy($from, $vendorDir . '/autoload.php');
			}
		}
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

		// @todo Validate input: trailing \ and /
		// @todo Read with json_decode() & file_get_contents()
		// @todo Write with json_encode() & file_put_contents()
		// @todo Remove referenced dir from vendor/

		// File format:
		// {"psr-4": {"rdx\\http\\": "/var/www/inc/HTTP/"}}
	}

}
