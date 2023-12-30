<?php

declare(strict_types=1);

namespace Contributte\Webpack\AssetNameResolver;

final class StaticAssetNameResolver implements AssetNameResolverInterface
{
	/**
	 * @param array<string, string> $resolutions
	 */
	public function __construct(
		private readonly array $resolutions,
	) {
	}

	public function resolveAssetName(string $asset): string
	{
		if (!isset($this->resolutions[$asset])) {
			throw new CannotResolveAssetNameException(\sprintf(
				"Asset '%s' was not found in the resolutions array",
				$asset
			));
		}

		return $this->resolutions[$asset];
	}
}
