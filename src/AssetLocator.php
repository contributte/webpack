<?php declare(strict_types = 1);

namespace Contributte\Webpack;

use Contributte\Webpack\AssetNameResolver\AssetNameResolverInterface;
use Contributte\Webpack\DevServer\DevServer;
use function in_array;
use function ltrim;
use function rtrim;
use function str_contains;
use function str_starts_with;

final class AssetLocator
{

	/**
	 * @param string[] $ignoredAssetNames
	 */
	public function __construct(
		private readonly BuildDirectoryProvider $directoryProvider,
		private readonly PublicPathProvider $publicPathProvider,
		private readonly AssetNameResolverInterface $assetResolver,
		private readonly DevServer $devServer,
		private readonly array $ignoredAssetNames,
	)
	{
	}

	public function locateInPublicPath(string $asset): string
	{
		return $this->locateInPath($this->publicPathProvider->getPublicPath(), $asset);
	}

	public function locateInBuildDirectory(string $asset): string
	{
		return $this->locateInPath($this->directoryProvider->getBuildDirectory(), $asset);
	}

	private function locateInPath(string $path, string $asset): string
	{
		if ($this->devServer->isAvailable() && in_array($asset, $this->ignoredAssetNames, true)) {
			return 'data:,';
		}

		$assetName = $this->assetResolver->resolveAssetName($asset);

		if ($this->isAbsoluteUrl($assetName)) {
			return $assetName;
		}

		return rtrim($path, '/') . '/' . ltrim($assetName, '/');
	}

	private function isAbsoluteUrl(string $url): bool
	{
		return str_contains($url, '://') || str_starts_with($url, '//');
	}

}
