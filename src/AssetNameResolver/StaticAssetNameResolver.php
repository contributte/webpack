<?php

declare(strict_types=1);

namespace Contributte\Webpack\AssetNameResolver;

final class StaticAssetNameResolver implements AssetNameResolverInterface
{
	/** @var array<string, string> */
	private array $resolutions;

	/**
	 * @param array<string, string> $resolutions
	 */
	public function __construct(array $resolutions)
	{
		$this->resolutions = $resolutions;
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
