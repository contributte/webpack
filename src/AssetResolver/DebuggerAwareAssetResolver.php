<?php

declare(strict_types = 1);

namespace Oops\WebpackNetteAdapter\AssetResolver;


final class DebuggerAwareAssetResolver implements AssetResolverInterface
{

	/**
	 * @var AssetResolverInterface
	 */
	private $inner;

	/**
	 * @var string[]
	 */
	private $resolvedAssets = [];


	public function __construct(AssetResolverInterface $inner)
	{
		$this->inner = $inner;
	}


	public function resolveAsset(string $asset): string
	{
		$resolved = $this->inner->resolveAsset($asset);
		$this->resolvedAssets[] = [$asset, $resolved];
		return $resolved;
	}


	public function getResolvedAssets(): array
	{
		return $this->resolvedAssets;
	}

}
