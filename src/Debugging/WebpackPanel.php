<?php declare(strict_types = 1);

namespace Contributte\Webpack\Debugging;

use Contributte\Webpack\AssetNameResolver\DebuggerAwareAssetNameResolver;
use Contributte\Webpack\DevServer\DevServer;
use Contributte\Webpack\PublicPathProvider;
use Tracy\IBarPanel;
use function ob_get_clean;
use function ob_start;

final class WebpackPanel implements IBarPanel
{

	public function __construct(
		private readonly PublicPathProvider $pathProvider,
		private readonly DebuggerAwareAssetNameResolver $assetResolver,
		private readonly DevServer $devServer,
	)
	{
	}

	public function getTab(): string
	{
		ob_start(function (): void {
		});
		// @phpcs:ignore SlevomatCodingStandard.Variables.UnusedVariable.UnusedVariable
		$devServer = $this->devServer;
		// @phpcs:ignore SlevomatCodingStandard.Variables.UnusedVariable.UnusedVariable
		$assets = $this->assetResolver->getResolvedAssets();
		require __DIR__ . '/templates/WebpackPanel.tab.phtml';

		return (string) ob_get_clean();
	}

	public function getPanel(): string
	{
		ob_start(function (): void {
		});
		// @phpcs:ignore SlevomatCodingStandard.Variables.UnusedVariable.UnusedVariable
		$devServer = $this->devServer;
		// @phpcs:ignore SlevomatCodingStandard.Variables.UnusedVariable.UnusedVariable
		$path = $this->pathProvider->getPublicPath();
		// @phpcs:ignore SlevomatCodingStandard.Variables.UnusedVariable.UnusedVariable
		$assets = $this->assetResolver->getResolvedAssets();
		require __DIR__ . '/templates/WebpackPanel.panel.phtml';

		return (string) ob_get_clean();
	}

}
