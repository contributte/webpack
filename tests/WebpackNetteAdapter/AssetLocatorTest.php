<?php

declare(strict_types = 1);

namespace OopsTests\WebpackNetteAdapter;

use Oops\WebpackNetteAdapter\AssetLocator;
use Oops\WebpackNetteAdapter\AssetResolver\AssetResolverInterface;
use Oops\WebpackNetteAdapter\BuildDirectoryProvider;
use Oops\WebpackNetteAdapter\PublicPathProvider;
use Tester\TestCase;


require_once __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 */
class AssetLocatorTest extends TestCase
{

	public function testLocateInBuildDirectory()
	{
		$directoryProvider = \Mockery::mock(BuildDirectoryProvider::class);
		$directoryProvider->shouldReceive('getBuildDirectory')
			->once()
			->andReturn('/home/user');

		$pathProvider = \Mockery::mock(PublicPathProvider::class);
		$directoryProvider->shouldReceive('getPath')
			->never();

		$assetResolver = \Mockery::mock(AssetResolverInterface::class);
		$assetResolver->shouldReceive('resolveAsset')
			->with('bar.js')
			->once()
			->andReturn('bar.js');

		$assetLocator = new AssetLocator($directoryProvider, $pathProvider, $assetResolver);
		Assert::same('/home/user/bar.js', $assetLocator->locateInBuildDirectory('bar.js'));
	}


	public function testLocateInPublicPath()
	{
		$directoryProvider = \Mockery::mock(BuildDirectoryProvider::class);
		$directoryProvider->shouldReceive('getBuildDirectory')
			->never();

		$pathProvider = \Mockery::mock(PublicPathProvider::class);
		$directoryProvider->shouldReceive('getPath')
			->once()
			->andReturn('/foo');

		$assetResolver = \Mockery::mock(AssetResolverInterface::class);
		$assetResolver->shouldReceive('resolveAsset')
			->with('bar.js')
			->once()
			->andReturn('bar.js');

		$assetLocator = new AssetLocator($directoryProvider, $pathProvider, $assetResolver);
		Assert::same('/foo/bar.js', $assetLocator->locateInPublicPath('bar.js'));
	}


	protected function tearDown()
	{
		\Mockery::close();
	}

}


(new AssetLocatorTest())->run();
