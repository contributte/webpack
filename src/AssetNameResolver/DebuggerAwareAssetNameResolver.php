<?php

declare(strict_types = 1);

namespace Oops\WebpackNetteAdapter\AssetNameResolver;


final class DebuggerAwareAssetNameResolver implements AssetNameResolverInterface
{

	/**
	 * @var AssetNameResolverInterface
	 */
	private $inner;

	/**
	 * @var string[]
	 */
	private $resolvedAssets = [];


	public function __construct(AssetNameResolverInterface $inner)
	{
		$this->inner = $inner;
	}


	public function resolveAssetName(string $asset): string
	{
		$resolved = $this->inner->resolveAssetName($asset);
		$this->resolvedAssets[] = [$asset, $resolved];
		return $resolved;
	}


	public function getResolvedAssets(): array
	{
		return $this->resolvedAssets;
	}

}
