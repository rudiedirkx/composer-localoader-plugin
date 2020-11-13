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

	public function deactivate(Composer $composer, IOInterface $io) {
	}

	public function uninstall(Composer $composer, IOInterface $io) {
	}

	/**
	 *
	 */
	public static function getSubscribedEvents() {
		return array(
			ScriptEvents::POST_AUTOLOAD_DUMP => 'onPostAutoloadDump',
			ScriptEvents::POST_INSTALL_CMD => 'onPostInstall',
			ScriptEvents::POST_UPDATE_CMD => 'onPostUpdate',
		);
	}

	/**
	 *
	 */
	public function onPostAutoloadDump(Event $event) {
		$this->symlinkAllReleaseCode($event);
	}

	/**
	 *
	 */
	public function onPostInstall(Event $event) {
		$this->symlinkAllReleaseCode($event);
	}

	/**
	 *
	 */
	public function onPostUpdate(Event $event) {
		$this->symlinkAllReleaseCode($event);
	}

	/**
	 *
	 */
	protected function symlinkAllReleaseCode(Event $event) {
		// @todo Use rdx\localoader\Localoader's logic

		$localoaded = $this->getLocaloadedPackages();

		foreach ($localoaded as $packageName => $source) {
			$packageDir = $this->getPackageDir($packageName);

			echo "Symlinking package " . $packageName . "\n";
			`rm -rf $packageDir`;
			`ln -fs $source $packageDir`;
		}
	}

	/**
	 *
	 */
	protected function getLocaloadedPackages() {
		$meta = array();
		if (file_exists($file = $this->getRootDir() . '/composer-locaload.json')) {
			if ($json = @file_get_contents($file)) {
				$meta = @json_decode($json, true) ?: array();
			}
		}

		return (array) @$meta['alias'];
	}

	/**
	 *
	 */
	protected function getPackageDir($packageName) {
		return $this->getVendorDir() . '/' . $packageName;
	}

	/**
	 *
	 */
	protected function getVendorDir() {
		$config = $this->composer->getConfig();
		return $config->get('vendor-dir');
	}

	/**
	 *
	 */
	protected function getRootDir() {
		return dirname($this->getVendorDir());
	}

}
