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
		$context = \stream_context_create(['ssl' => ['verify_peer' => false, 'verify_peer_name' => false]]); // webpack-dev-server uses self-signed certificate
		$manifest = @\file_get_contents($path, false, $context); // @ - errors handled by custom exception

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
