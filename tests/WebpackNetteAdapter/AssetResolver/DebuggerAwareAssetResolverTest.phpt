<?php

declare(strict_types = 1);

namespace OopsTests\WebpackNetteAdapter\AssetResolver;

use Oops\WebpackNetteAdapter\AssetResolver\DebuggerAwareAssetResolver;
use Oops\WebpackNetteAdapter\AssetResolver\IdentityAssetResolver;
use Tester\Assert;
use Tester\TestCase;


require_once __DIR__ . '/../../bootstrap.php';


/**
 * @testCase
 */
class DebuggerAwareAssetResolverTest extends TestCase
{

	public function testResolver()
	{
		$resolver = new DebuggerAwareAssetResolver(new IdentityAssetResolver());
		$resolver->resolveAsset('asset.js');
		$resolver->resolveAsset('asset.css');

		Assert::same([
			['asset.js', 'asset.js'],
			['asset.css', 'asset.css'],
		], $resolver->getResolvedAssets());
	}

}


(new DebuggerAwareAssetResolverTest())->run();
