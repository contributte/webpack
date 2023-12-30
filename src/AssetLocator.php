<?php

declare(strict_types=1);

namespace Contributte\Webpack;

use Contributte\Webpack\AssetNameResolver\AssetNameResolverInterface;
use Contributte\Webpack\DevServer\DevServer;

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
	) {
	}

	private function locateInPath(string $path, string $asset): string
	{
		if ($this->devServer->isAvailable() && \in_array($asset, $this->ignoredAssetNames, true)) {
			return 'data:,';
		}

		$assetName = $this->assetResolver->resolveAssetName($asset);

		if ($this->isAbsoluteUrl($assetName)) {
			return $assetName;
		}

		return \rtrim($path, '/') . '/' . \ltrim($assetName, '/');
	}

	public function locateInPublicPath(string $asset): string
	{
		return $this->locateInPath($this->publicPathProvider->getPublicPath(), $asset);
	}

	public function locateInBuildDirectory(string $asset): string
	{
		return $this->locateInPath($this->directoryProvider->getBuildDirectory(), $asset);
	}

	private function isAbsoluteUrl(string $url): bool
	{
		return \str_contains($url, '://') || \str_starts_with($url, '//');
	}
}
