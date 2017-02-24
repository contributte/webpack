<?php

declare(strict_types = 1);

namespace Oops\WebpackNetteAdapter\AssetResolver;


final class IdentityAssetResolver implements AssetResolverInterface
{

	public function resolveAsset(string $asset): string
	{
		return $asset;
	}

}
