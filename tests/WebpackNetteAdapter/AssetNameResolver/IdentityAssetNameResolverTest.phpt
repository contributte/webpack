<?php

declare(strict_types = 1);

namespace OopsTests\WebpackNetteAdapter\AssetNameResolver;

use Oops\WebpackNetteAdapter\AssetNameResolver\IdentityAssetNameResolver;
use Tester\Assert;
use Tester\TestCase;


require_once __DIR__ . '/../../bootstrap.php';


/**
 * @testCase
 */
class IdentityAssetNameResolverTest extends TestCase
{

	public function testResolver(): void
	{
		$resolver = new IdentityAssetNameResolver();
		Assert::same('asset.js', $resolver->resolveAssetName('asset.js'));
	}

}


(new IdentityAssetNameResolverTest())->run();
