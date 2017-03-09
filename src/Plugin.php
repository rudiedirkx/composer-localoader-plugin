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
			ScriptEvents::POST_INSTALL_CMD => 'onPostInstall',
			ScriptEvents::POST_UPDATE_CMD => 'onPostUpdate',
		);
	}

	/**
	 *
	 */
	public function onPostAutoloadDump(Event $event) {
		$config = $event->getComposer()->getConfig();
		$vendorDir = $config->get('vendor-dir');

		// @todo Use rdx\localoader\Localoader's logic

		$global = $config->get('home') == dirname($vendorDir);

		if (!$global) {
			if (file_exists($from = dirname(__DIR__) . '/autoload-dev.php')) {
				rename($vendorDir . '/autoload.php', $vendorDir . '/autoload-composer.php');
				copy($from, $vendorDir . '/autoload.php');
			}
		}

		// $this->removeAllReleaseCode($event);
		$this->symlinkAllReleaseCode($event);
	}

	/**
	 *
	 */
	public function onPostInstall(Event $event) {
		// $this->removeAllReleaseCode($event);
		$this->symlinkAllReleaseCode($event);
	}

	/**
	 *
	 */
	public function onPostUpdate(Event $event) {
		// $this->removeAllReleaseCode($event);
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
	protected function removeAllReleaseCode(Event $event) {
		// @todo Use rdx\localoader\Localoader's logic

		// @todo Check if we should remove anything at all: #6

		// STOP! Don't remove ANYTHING if we have any aliases, because we don't want
		// to remove code inside the aliased sources.
		if ($this->getLocaloadedPackages()) {
			return;
		}

		$localoaded = $this->getLocaloadedPackageDirs();

		$unprefix = function($dir) {
			return preg_replace('#' . getcwd() . '/vendor/#', '', $dir);
		};

		foreach ($localoaded as $packageNamespace => $dir) {
			echo "Removing code from " . $unprefix($dir) . "\n";
			`rm -rf $dir`;
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
	protected function getLocaloadedNamespaces() {
		$meta = array();
		if (file_exists($file = $this->getRootDir() . '/composer-locaload.json')) {
			if ($json = @file_get_contents($file)) {
				$meta = @json_decode($json, true) ?: array();
			}
		}

		$namespaces = array_merge(array_keys((array) @$meta['psr-0']), array_keys((array) @$meta['psr-4']));
		$namespaces = array_map([$this, 'normalizeNamespace'], $namespaces);
		return $namespaces;
	}

	/**
	 *
	 */
	protected function getPackageNames() {
		$locker = $this->composer->getLocker();
		$lockData = $locker->getLockData();

		$packageNames = array_column($lockData['packages'], 'name');
		return $packageNames;
	}

	/**
	 *
	 */
	protected function normalizeNamespace($namespace) {
		return str_replace('\\', '/', trim($namespace, '\\/'));
	}

	/**
	 *
	 */
	protected function getLocaloadedPackageDirs() {
		$localNamespaces = $this->getLocaloadedNamespaces();

		$localoaded = [];
		foreach ($this->getPackageNames() as $packageName) {
			foreach ($this->getPackageAutoload($packageName) as $namespace => $dirs) {
				if (in_array($this->normalizeNamespace($namespace), $localNamespaces)) {
					foreach ($dirs as $i => $dir) {
						$localoaded["$packageName:$namespace:$i"] = $this->getPackageDir($packageName) . '/'. trim($dir, '\\/');
					}
				}
			}
		}

		return $localoaded;
	}

	/**
	 *
	 */
	protected function getPackageAutoload($packageName) {
		$file = $this->getPackageDir($packageName) . '/composer.json';
		$data = json_decode(file_get_contents($file), true);

		$namespaces = [];
		foreach ((array) @$data['autoload']['psr-0'] as $namespace => $dirs) {
			$namespaces[$namespace] = array_map(function($dir) use ($namespace) {
				return rtrim($dir, '\\/') . '/' . str_replace('\\', '/', $namespace);
			}, (array) $dirs);
		}
		foreach ((array) @$data['autoload']['psr-4'] as $namespace => $dirs) {
			$namespaces[$namespace] = array_map(function($dir) {
				return rtrim($dir, '\\/');
			}, (array) $dirs);
		}

		return $namespaces;
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
