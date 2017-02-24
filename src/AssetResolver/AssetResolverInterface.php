<?php

declare(strict_types = 1);

namespace Oops\WebpackNetteAdapter\AssetResolver;


interface AssetResolverInterface
{

	public function resolveAsset(string $asset): string;

}
