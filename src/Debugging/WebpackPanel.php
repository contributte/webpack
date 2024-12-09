<?php

declare(strict_types=1);

namespace Contributte\Webpack\Debugging;

use Contributte\Webpack\AssetNameResolver\DebuggerAwareAssetNameResolver;
use Contributte\Webpack\DevServer\DevServer;
use Contributte\Webpack\PublicPathProvider;
use Tracy\IBarPanel;

final class WebpackPanel implements IBarPanel
{
	public function __construct(
		private readonly PublicPathProvider $pathProvider,
		private readonly DebuggerAwareAssetNameResolver $assetResolver,
		private readonly DevServer $devServer,
	) {
	}

	public function getTab(): string
	{
		\ob_start(function (): void {
		});
		$devServer = $this->devServer;
		$assets = $this->assetResolver->getResolvedAssets();
		require __DIR__ . '/templates/WebpackPanel.tab.phtml';
		return (string) \ob_get_clean();
	}

	public function getPanel(): string
	{
		\ob_start(function (): void {
		});
		$devServer = $this->devServer;
		$path = $this->pathProvider->getPublicPath();
		$assets = $this->assetResolver->getResolvedAssets();
		require __DIR__ . '/templates/WebpackPanel.panel.phtml';
		return (string) \ob_get_clean();
	}
}
