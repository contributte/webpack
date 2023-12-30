<?php

declare(strict_types=1);

namespace Contributte\Webpack\AssetNameResolver;

final class DebuggerAwareAssetNameResolver implements AssetNameResolverInterface
{
	/** @var array<array{string, string}> */
	private array $resolvedAssets = [];

	public function __construct(
		private readonly AssetNameResolverInterface $inner,
	) {
	}

	public function resolveAssetName(string $asset): string
	{
		$resolved = $this->inner->resolveAssetName($asset);
		$this->resolvedAssets[] = [$asset, $resolved];
		return $resolved;
	}

	/**
	 * @return array<array{string, string}>
	 */
	public function getResolvedAssets(): array
	{
		return $this->resolvedAssets;
	}
}
