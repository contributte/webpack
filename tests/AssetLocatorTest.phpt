<?php

declare(strict_types=1);

namespace Contributte\Webpack\Tests;

use Contributte\Webpack\AssetLocator;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/bootstrap.php';

/**
 * @testCase
 */
final class AssetLocatorTest extends TestCase
{
	public function testLocateInBuildDirectory(): void
	{
		$directoryProvider = createBuildDirectoryProvider('/home/user');
		$pathProvider = createPublicPathProvider('/foo');
		$assetResolver = createAssetNameResolver(['bar.js' => 'bar.js']);
		$devServer = createDisabledDevServer();

		$assetLocator = new AssetLocator($directoryProvider, $pathProvider, $assetResolver, $devServer, []);
		Assert::same('/home/user/bar.js', $assetLocator->locateInBuildDirectory('bar.js'));
	}

	public function testLocateInPublicPath(): void
	{
		$directoryProvider = createBuildDirectoryProvider('/home/user');
		$pathProvider = createPublicPathProvider('/foo');
		$assetResolver = createAssetNameResolver(['bar.js' => 'bar.js']);
		$devServer = createDisabledDevServer();

		$assetLocator = new AssetLocator($directoryProvider, $pathProvider, $assetResolver, $devServer, []);
		Assert::same('/foo/bar.js', $assetLocator->locateInPublicPath('bar.js'));
	}

	public function testIgnoredAssets(): void
	{
		$directoryProvider = createBuildDirectoryProvider('/home/user');
		$pathProvider = createPublicPathProvider('/foo');
		$assetResolver = createAssetNameResolver(['bar.js' => 'bar.js']);
		$devServer = createEnabledDevServer(true);

		$assetLocator = new AssetLocator($directoryProvider, $pathProvider, $assetResolver, $devServer, ['foo.css']);
		Assert::same('data:,', $assetLocator->locateInBuildDirectory('foo.css'));
		Assert::same('data:,', $assetLocator->locateInPublicPath('foo.css'));
		Assert::same('/home/user/bar.js', $assetLocator->locateInBuildDirectory('bar.js'));
		Assert::same('/foo/bar.js', $assetLocator->locateInPublicPath('bar.js'));
	}
}

(new AssetLocatorTest())->run();
