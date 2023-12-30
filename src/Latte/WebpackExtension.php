<?php

declare(strict_types=1);

namespace Contributte\Webpack\Latte;

use Contributte\Webpack\AssetLocator;
use Latte\Extension;

final class WebpackExtension extends Extension
{
	public function __construct(
		private readonly AssetLocator $assetLocator,
	) {
	}

	public function getTags(): array
	{
		return [
			'webpack' => [WebpackNode::class, 'create'],
		];
	}

	public function getProviders(): array
	{
		return [
			'webpackAssetLocator' => $this->assetLocator,
		];
	}
}
