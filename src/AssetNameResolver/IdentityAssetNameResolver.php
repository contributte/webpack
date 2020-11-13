<?php

declare(strict_types=1);

namespace Contributte\Webpack\AssetNameResolver;

final class IdentityAssetNameResolver implements AssetNameResolverInterface
{
	public function resolveAssetName(string $asset): string
	{
		return $asset;
	}
}
