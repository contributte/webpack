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
			$manifest = @\file_get_contents($path); // @ - errors handled by custom exception
		} else {
			$ch = \curl_init($path);

			if ($ch === false) {
				$manifest = false;
			} else {
				\curl_setopt_array($ch, [
					\CURLOPT_RETURNTRANSFER => true,
					\CURLOPT_FAILONERROR => true,

					// allow self-signed certificates
					\CURLOPT_SSL_VERIFYHOST => 0,
					\CURLOPT_SSL_VERIFYPEER => false,
				]);
				/** @var string|false $manifest */
				$manifest = \curl_exec($ch);

				if ($manifest === false) {
					$errorMessage = \curl_error($ch);
				}

				\curl_close($ch);
			}
		}

		if ($manifest === false) {
			throw new CannotLoadManifestException(\sprintf(
				"Manifest file '%s' could not be loaded: %s",
				$path,
				$errorMessage ?? (\error_get_last()['message'] ?? 'unknown error')
			));
		}

		return $this->manifestMapper->map(Json::decode($manifest, Json::FORCE_ARRAY));
	}

	public function getManifestPath(string $fileName): string
	{
		return $this->directoryProvider->getBuildDirectory() . '/' . $fileName;
	}
}
