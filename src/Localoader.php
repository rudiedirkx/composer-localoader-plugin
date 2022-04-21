<?php

namespace rdx\localoader;

use Composer\Json\JsonFile;
use rdx\localoader\LocaloaderException;

class Localoader {

	const FILE = 'composer.locaload';

	protected $dir;
	protected $file;

	/**
	 *
	 */
	public function __construct($dir) {
		if ( !file_exists($dir . '/composer.json') ) {
			throw new LocaloaderException("Localoader must be run in a project's root.");
		}

		$this->dir = $dir;
		$this->file = new JsonFile($dir . '/' . self::FILE);
	}

	/**
	 *
	 */
	public function getLocaLoads() {
		$default = [
			'alias' => [],
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
	public function validatePackage($package) {
		$package = self::encodePackage($package);
		$packageDir = $this->dir . '/vendor/' . $package;
		return strpos($package, '/') !== false && is_dir($packageDir);
	}

	/**
	 *
	 */
	public function validateSource($source) {
		$source = self::encodeSource($source);
		return file_exists("$source/composer.json");
	}

	/**
	 *
	 */
	public function addPackageException($package, $source) {
		$data = $this->getLocaLoads();
		$data["alias"][$package] = self::encodeSource($source);

		return $this->saveLocaloads($data);
	}

	/**
	 *
	 */
	static public function encodeSource($source) {
		return rtrim(str_replace('\\', '/', $source), '/');
	}

	/**
	 *
	 */
	static public function encodePackage($source) {
		return trim(str_replace('\\', '/', $source), '/');
	}

	// @todo Remove localoaded code (called from rdx\localoader\Plugin)

}
