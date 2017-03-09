<?php

namespace rdx\localoader;

use Composer\Json\JsonFile;
use rdx\localoader\LocaloaderException;

class Localoader {

	const FILE = 'composer-locaload.json';

	protected $file;

	/**
	 *
	 */
	public function __construct($dir) {
		if ( !file_exists($dir . '/composer.json') ) {
			throw new LocaloaderException("Localoader must be run in a project's root.");
		}

		$this->file = new JsonFile($dir . '/' . self::FILE);
	}

	/**
	 *
	 */
	public function getLocaLoads() {
		$default = [
			'alias' => [],
			'psr-0' => [],
			'psr-4' => [],
		];

		if ($this->file->exists()) {
			$default = $this->file->read() + $default;
			uksort($default, 'strnatcasecmp');
		}

		return $default;
	}

	/**
	 *
	 */
	protected function saveLocaloads(array $data) {
		return $this->file->write(array_filter($data));
	}

	/**
	 *
	 */
	public function addNamespaceException($psr, $namespace, $source) {
		$data = $this->getLocaLoads();
		$data["psr-$psr"][$namespace] = $source;

		return $this->saveLocaloads($data);
	}

	/**
	 *
	 */
	public function addPackageException($package, $source) {
		$data = $this->getLocaLoads();
		$data["alias"][$package] = $source;

		return $this->saveLocaloads($data);
	}

	/**
	 *
	 */
	static public function encodeNamespace($namespace) {
		return trim(str_replace('\\', '/', $namespace), '/');
	}

	/**
	 *
	 */
	static public function encodeSource($source) {
		return rtrim(str_replace('\\', '/', $source), '/');
	}

	// @todo Remove localoaded code (called from rdx\localoader\Plugin)

}
