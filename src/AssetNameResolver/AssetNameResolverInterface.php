<?php

declare(strict_types=1);

namespace Contributte\Webpack\AssetNameResolver;

interface AssetNameResolverInterface
{
	public function resolveAssetName(string $asset): string;
}
