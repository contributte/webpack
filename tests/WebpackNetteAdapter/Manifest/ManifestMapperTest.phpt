<?php

declare(strict_types = 1);

namespace OopsTests\WebpackNetteAdapter\Manifest;

use Oops\WebpackNetteAdapter\BuildDirectoryProvider;
use Oops\WebpackNetteAdapter\Manifest\AssetsWebpackPluginMapper;
use Oops\WebpackNetteAdapter\Manifest\CannotLoadManifestException;
use Oops\WebpackNetteAdapter\Manifest\ManifestLoader;
use Oops\WebpackNetteAdapter\Manifest\ManifestMapper;
use Tester\Assert;
use Tester\TestCase;


require_once __DIR__ . '/../../bootstrap.php';


/**
 * @testCase
 */
class ManifestLoaderTest extends TestCase
{

    public function testLoaderCallsMapper(): void
	{
		$buildDirProvider = \Mockery::mock(BuildDirectoryProvider::class);
		$buildDirProvider->shouldReceive('getBuildDirectory')->andReturn(__DIR__);

		$mapperResult = ['main.js' => 'resolved.asset.js'];
		$mapperMock = \Mockery::mock(ManifestMapper::class);
		$mapperMock->shouldReceive('map')->andReturn($mapperResult);

		$manifestLoader = new ManifestLoader($buildDirProvider, $mapperMock);

		Assert::same($mapperResult, $manifestLoader->loadManifest('assetsManifest.json'));

		\Mockery::close();
	}

	public function testAssetsWebpackPluginMapper(): void
	{
		$buildDirProvider = \Mockery::mock(BuildDirectoryProvider::class);
		$buildDirProvider->shouldReceive('getBuildDirectory')->andReturn(__DIR__);
		$manifestLoader = new ManifestLoader($buildDirProvider, new AssetsWebpackPluginMapper());

		Assert::same(['main.js' => 'resolved.asset.js'], $manifestLoader->loadManifest('assetsManifest.json'));

		\Mockery::close();
	}

}


(new ManifestLoaderTest())->run();
