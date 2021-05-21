<?php

declare(strict_types=1);

namespace Contributte\Webpack\Manifest;

use Contributte\Webpack\BuildDirectoryProvider;
use Nette\Utils\Json;

/**
 * @internal
 */
final class ManifestLoader
{
	private BuildDirectoryProvider $directoryProvider;

	private ManifestMapper $manifestMapper;

	public function __construct(BuildDirectoryProvider $directoryProvider, ManifestMapper $manifestMapper)
	{
		$this->directoryProvider = $directoryProvider;
		$this->manifestMapper = $manifestMapper;
	}

	/**
	 * @throws CannotLoadManifestException
	 * @return array<string, string>
	 */
	public function loadManifest(string $fileName): array
	{
		$path = $this->getManifestPath($fileName);

		if (\is_file($path)) {
			$manifest = \file_get_contents($path);
		} else {
			$ch = \curl_init($path);
			\curl_setopt_array($ch, [
				\CURLOPT_RETURNTRANSFER => true,

				// allow self-signed certificates
				\CURLOPT_SSL_VERIFYHOST => 0,
				\CURLOPT_SSL_VERIFYPEER => false,
			]);
			/** @var string|false $manifest */
			$manifest = \curl_exec($ch);
			\curl_close($ch);
		}

		if ($manifest === false) {
			throw new CannotLoadManifestException(\sprintf(
				"Manifest file '%s' could not be loaded: %s",
				$path,
				\error_get_last()['message'] ?? 'unknown error'
			));
		}

		return $this->manifestMapper->map(Json::decode($manifest, Json::FORCE_ARRAY));
	}

	public function getManifestPath(string $fileName): string
	{
		return $this->directoryProvider->getBuildDirectory() . '/' . $fileName;
	}
}
