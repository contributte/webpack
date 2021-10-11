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
		$assetResolver = createAssetNameResolver(['bar.js' => 'bar.js', 'foo1.js' => 'http://localhost/foo1.js', 'foo2.js' => '//localhost/foo2.js']);
		$devServer = createDisabledDevServer();

		$assetLocator = new AssetLocator($directoryProvider, $pathProvider, $assetResolver, $devServer, []);
		Assert::same('/home/user/bar.js', $assetLocator->locateInBuildDirectory('bar.js'));
		Assert::same('http://localhost/foo1.js', $assetLocator->locateInBuildDirectory('foo1.js'));
		Assert::same('//localhost/foo2.js', $assetLocator->locateInBuildDirectory('foo2.js'));
	}

	public function testLocateInPublicPath(): void
	{
		$directoryProvider = createBuildDirectoryProvider('/home/user');
		$pathProvider = createPublicPathProvider('/foo');
		$assetResolver = createAssetNameResolver(['bar.js' => 'bar.js', 'foo1.js' => 'http://localhost/foo1.js', 'foo2.js' => '//localhost/foo2.js']);
		$devServer = createDisabledDevServer();

		$assetLocator = new AssetLocator($directoryProvider, $pathProvider, $assetResolver, $devServer, []);
		Assert::same('/foo/bar.js', $assetLocator->locateInPublicPath('bar.js'));
		Assert::same('http://localhost/foo1.js', $assetLocator->locateInBuildDirectory('foo1.js'));
		Assert::same('//localhost/foo2.js', $assetLocator->locateInBuildDirectory('foo2.js'));
	}

	public function testIgnoredAssets(): void
	{
		$directoryProvider = createBuildDirectoryProvider('/home/user');
		$pathProvider = createPublicPathProvider('/foo');
		$assetResolver = createAssetNameResolver(['bar.js' => 'bar.js', 'foo1.js' => 'http://localhost/foo1.js', 'foo2.js' => '//localhost/foo2.js']);
		$devServer = createEnabledDevServer(true);

		$assetLocator = new AssetLocator($directoryProvider, $pathProvider, $assetResolver, $devServer, ['foo.css']);
		Assert::same('data:,', $assetLocator->locateInBuildDirectory('foo.css'));
		Assert::same('data:,', $assetLocator->locateInPublicPath('foo.css'));
		Assert::same('/home/user/bar.js', $assetLocator->locateInBuildDirectory('bar.js'));
		Assert::same('/foo/bar.js', $assetLocator->locateInPublicPath('bar.js'));
		Assert::same('http://localhost/foo1.js', $assetLocator->locateInBuildDirectory('foo1.js'));
		Assert::same('http://localhost/foo1.js', $assetLocator->locateInPublicPath('foo1.js'));
		Assert::same('//localhost/foo2.js', $assetLocator->locateInBuildDirectory('foo2.js'));
		Assert::same('//localhost/foo2.js', $assetLocator->locateInPublicPath('foo2.js'));
		Assert::same('data:,', $assetLocator->locateInBuildDirectory('foo.css'));
	}
}

(new AssetLocatorTest())->run();
