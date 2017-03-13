<?php

declare(strict_types = 1);

namespace OopsTests\WebpackNetteAdapter\AssetNameResolver;

use Oops\WebpackNetteAdapter\AssetNameResolver\CannotResolveAssetNameException;
use Oops\WebpackNetteAdapter\AssetNameResolver\ManifestAssetNameResolver;
use Oops\WebpackNetteAdapter\BuildDirectoryProvider;
use Oops\WebpackNetteAdapter\DevServer;
use Tester\Assert;
use Tester\TestCase;


require_once __DIR__ . '/../../bootstrap.php';


/**
 * @testCase
 */
class ManifestAssetNameResolverTest extends TestCase
{

	public function testResolver()
	{
		$devServer = \Mockery::mock(DevServer::class);
		$devServer->shouldReceive('isAvailable')->andReturn(FALSE);
		$resolver = new ManifestAssetNameResolver('manifest.json', new BuildDirectoryProvider(__DIR__, $devServer));
		Assert::same('resolved.asset.js', $resolver->resolveAssetName('asset.js'));

		Assert::throws(function () use ($resolver) {
			$resolver->resolveAssetName('unknownAsset.js');
		}, CannotResolveAssetNameException::class);

		\Mockery::close();
	}

}


(new ManifestAssetNameResolverTest())->run();
