<?php

declare(strict_types = 1);

namespace OopsTests\WebpackNetteAdapter\AssetResolver;

use Oops\WebpackNetteAdapter\AssetResolver\IdentityAssetResolver;
use Tester\Assert;
use Tester\TestCase;


require_once __DIR__ . '/../../bootstrap.php';


/**
 * @testCase
 */
class IdentityAssetResolverTest extends TestCase
{

	public function testResolver()
	{
		$resolver = new IdentityAssetResolver();
		Assert::same('asset.js', $resolver->resolveAsset('asset.js'));
	}

}


(new IdentityAssetResolverTest())->run();
