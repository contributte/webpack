<?php

declare(strict_types = 1);

namespace OopsTests\WebpackNetteAdapter\AssetNameResolver;

use Oops\WebpackNetteAdapter\AssetNameResolver\CannotResolveAssetNameException;
use Oops\WebpackNetteAdapter\AssetNameResolver\ManifestAssetNameResolver;
use Oops\WebpackNetteAdapter\Manifest\CannotLoadManifestException;
use Oops\WebpackNetteAdapter\Manifest\ManifestLoader;
use Tester\Assert;
use Tester\TestCase;


require_once __DIR__ . '/../../bootstrap.php';


/**
 * @testCase
 */
class ManifestAssetNameResolverTest extends TestCase
{

	public function testResolver(): void
	{
		$manifestLoader = \Mockery::mock(ManifestLoader::class);
		$manifestLoader->shouldReceive('loadManifest')
			->with('manifest.json')
			->andReturn(['asset.js' => 'resolved.asset.js']);

		$manifestLoader->shouldReceive('getManifestPath')
			->andReturn('/path/to/manifest.json');

		$resolver = new ManifestAssetNameResolver('manifest.json', $manifestLoader);
		Assert::same('resolved.asset.js', $resolver->resolveAssetName('asset.js'));

		Assert::throws(function () use ($resolver) {
			$resolver->resolveAssetName('unknownAsset.js');
		}, CannotResolveAssetNameException::class);
	}


	public function testCannotLoadManifest(): void
	{
		$manifestLoader = \Mockery::mock(ManifestLoader::class);
		$manifestLoader->shouldReceive('loadManifest')
			->with('manifest.json')
			->andThrow(new CannotLoadManifestException());

		$resolver = new ManifestAssetNameResolver('manifest.json', $manifestLoader);

		Assert::throws(function () use ($resolver) {
			$resolver->resolveAssetName('asset.js');
		}, CannotResolveAssetNameException::class);
	}


	protected function tearDown(): void
	{
		parent::tearDown();
		\Mockery::close();
	}

}


(new ManifestAssetNameResolverTest())->run();
