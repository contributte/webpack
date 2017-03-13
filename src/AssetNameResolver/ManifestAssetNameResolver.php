<?php

declare(strict_types = 1);

namespace Oops\WebpackNetteAdapter\AssetNameResolver;

use Nette\Utils\Json;
use Oops\WebpackNetteAdapter\BuildDirectoryProvider;


final class ManifestAssetNameResolver implements AssetNameResolverInterface
{

	/**
	 * @var string
	 */
	private $manifestPath;

	/**
	 * @var string[]
	 */
	private $manifestCache;


	public function __construct(string $manifestName, BuildDirectoryProvider $directoryProvider)
	{
		$this->manifestPath = $directoryProvider->getBuildDirectory() . '/' . $manifestName;
	}


	public function resolveAssetName(string $asset): string
	{
		if ($this->manifestCache === NULL) {
			$this->loadManifest();
		}

		if ( ! isset($this->manifestCache[$asset])) {
			throw new CannotResolveAssetNameException(sprintf(
				"Asset '%s' was not found in the manifest file '%s'",
				$asset, $this->manifestPath
			));
		}

		return $this->manifestCache[$asset];
	}


	private function loadManifest()
	{
		$context = stream_context_create(['ssl' => ['verify_peer' => FALSE]]);
		$manifest = file_get_contents($this->manifestPath, FALSE, $context);
		if ($manifest === FALSE) {
			throw new CannotResolveAssetNameException(sprintf(
				"Manifest file '%s' could not be loaded: %s",
				$this->manifestPath, error_get_last()['message'] ?? 'unknown error'
			));
		}

		$this->manifestCache = Json::decode($manifest, Json::FORCE_ARRAY);
	}

}
