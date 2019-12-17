<?php

declare(strict_types = 1);

namespace Oops\WebpackNetteAdapter\Manifest;

use Nette\Utils\Json;
use Oops\WebpackNetteAdapter\BuildDirectoryProvider;


/**
 * @internal
 */
class ManifestLoader
{

	/**
	 * @var BuildDirectoryProvider
	 */
	private $directoryProvider;


	public function __construct(BuildDirectoryProvider $directoryProvider)
	{
		$this->directoryProvider = $directoryProvider;
	}


	/**
	 * @throws CannotLoadManifestException
	 * @return array<string, string>
	 */
	public function loadManifest(string $fileName): array
	{
		$path = $this->getManifestPath($fileName);
		$context = \stream_context_create(['ssl' => ['verify_peer' => FALSE]]); // webpack-dev-server uses self-signed certificate
		$manifest = @\file_get_contents($path, FALSE, $context); // @ - errors handled by custom exception

		if ($manifest === FALSE) {
			throw new CannotLoadManifestException(\sprintf(
				"Manifest file '%s' could not be loaded: %s",
				$path, \error_get_last()['message'] ?? 'unknown error'
			));
		}

		return Json::decode($manifest, Json::FORCE_ARRAY);
	}


	public function getManifestPath(string $fileName): string
	{
		return $this->directoryProvider->getBuildDirectory() . '/' . $fileName;
	}

}
