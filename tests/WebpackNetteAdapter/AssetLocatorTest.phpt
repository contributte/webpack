<?php

declare(strict_types = 1);

namespace OopsTests\WebpackNetteAdapter;

use Oops\WebpackNetteAdapter\AssetLocator;
use Oops\WebpackNetteAdapter\AssetNameResolver\AssetNameResolverInterface;
use Oops\WebpackNetteAdapter\BuildDirectoryProvider;
use Oops\WebpackNetteAdapter\DevServer\DevServer;
use Oops\WebpackNetteAdapter\PublicPathProvider;
use Tester\Assert;
use Tester\TestCase;


require_once __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 */
class AssetLocatorTest extends TestCase
{

	public function testLocateInBuildDirectory(): void
	{
		$directoryProvider = \Mockery::mock(BuildDirectoryProvider::class);
		$directoryProvider->shouldReceive('getBuildDirectory')
			->once()
			->andReturn('/home/user');

		$pathProvider = \Mockery::mock(PublicPathProvider::class);
		$pathProvider->shouldReceive('getPublicPath')
			->never();

		$assetResolver = \Mockery::mock(AssetNameResolverInterface::class);
		$assetResolver->shouldReceive('resolveAssetName')
			->with('bar.js')
			->once()
			->andReturn('bar.js');

		$devServer = \Mockery::mock(DevServer::class);
		$devServer->shouldReceive('isAvailable')->andReturn(FALSE);

		$assetLocator = new AssetLocator($directoryProvider, $pathProvider, $assetResolver, $devServer, []);
		Assert::same('/home/user/bar.js', $assetLocator->locateInBuildDirectory('bar.js'));
	}


	public function testLocateInPublicPath(): void
	{
		$directoryProvider = \Mockery::mock(BuildDirectoryProvider::class);
		$directoryProvider->shouldReceive('getBuildDirectory')
			->never();

		$pathProvider = \Mockery::mock(PublicPathProvider::class);
		$pathProvider->shouldReceive('getPublicPath')
			->once()
			->andReturn('/foo');

		$assetResolver = \Mockery::mock(AssetNameResolverInterface::class);
		$assetResolver->shouldReceive('resolveAssetName')
			->with('bar.js')
			->once()
			->andReturn('bar.js');

		$devServer = \Mockery::mock(DevServer::class);
		$devServer->shouldReceive('isAvailable')->andReturn(FALSE);

		$assetLocator = new AssetLocator($directoryProvider, $pathProvider, $assetResolver, $devServer, []);
		Assert::same('/foo/bar.js', $assetLocator->locateInPublicPath('bar.js'));
	}


	public function testIgnoredAssets(): void
	{
		$directoryProvider = \Mockery::mock(BuildDirectoryProvider::class);
		$directoryProvider->shouldReceive('getBuildDirectory')
			->once()
			->andReturn('/home/user');

		$pathProvider = \Mockery::mock(PublicPathProvider::class);
		$pathProvider->shouldReceive('getPublicPath')
			->once()
			->andReturn('/foo');

		$assetResolver = \Mockery::mock(AssetNameResolverInterface::class);
		$assetResolver->shouldReceive('resolveAssetName')
			->with('bar.js')
			->twice()
			->andReturn('bar.js');

		$assetResolver->shouldReceive('resolveAssetName')
			->with('foo.css')
			->never();

		$devServer = \Mockery::mock(DevServer::class);
		$devServer->shouldReceive('isAvailable')->andReturn(TRUE);

		$assetLocator = new AssetLocator($directoryProvider, $pathProvider, $assetResolver, $devServer, ['foo.css']);
		Assert::same('data:,', $assetLocator->locateInBuildDirectory('foo.css'));
		Assert::same('data:,', $assetLocator->locateInPublicPath('foo.css'));
		Assert::same('/home/user/bar.js', $assetLocator->locateInBuildDirectory('bar.js'));
		Assert::same('/foo/bar.js', $assetLocator->locateInPublicPath('bar.js'));
	}


	protected function tearDown(): void
	{
		\Mockery::close();
	}

}


(new AssetLocatorTest())->run();
