<?php

declare(strict_types=1);

namespace Contributte\Webpack\Tests\AssetNameResolver;

use Contributte\Webpack\AssetNameResolver\IdentityAssetNameResolver;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
final class IdentityAssetNameResolverTest extends TestCase
{
	public function testResolver(): void
	{
		$resolver = new IdentityAssetNameResolver();
		Assert::same('asset.js', $resolver->resolveAssetName('asset.js'));
	}
}

(new IdentityAssetNameResolverTest())->run();
