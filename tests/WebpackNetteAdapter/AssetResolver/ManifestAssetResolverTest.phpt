<?php

declare(strict_types = 1);

namespace OopsTests\WebpackNetteAdapter\AssetResolver;

use Oops\WebpackNetteAdapter\AssetResolver\CannotResolveAssetException;
use Oops\WebpackNetteAdapter\AssetResolver\ManifestAssetResolver;
use Oops\WebpackNetteAdapter\BuildDirectoryProvider;
use Oops\WebpackNetteAdapter\DevServer;
use Tester\Assert;
use Tester\TestCase;


require_once __DIR__ . '/../../bootstrap.php';


/**
 * @testCase
 */
class ManifestAssetResolverTest extends TestCase
{

	public function testResolver()
	{
		$devServer = \Mockery::mock(DevServer::class);
		$devServer->shouldReceive('isAvailable')->andReturn(FALSE);
		$resolver = new ManifestAssetResolver('manifest.json', new BuildDirectoryProvider(__DIR__, $devServer));
		Assert::same('resolved.asset.js', $resolver->resolveAsset('asset.js'));

		Assert::throws(function () use ($resolver) {
			$resolver->resolveAsset('unknownAsset.js');
		}, CannotResolveAssetException::class);

		\Mockery::close();
	}

}


(new ManifestAssetResolverTest())->run();
