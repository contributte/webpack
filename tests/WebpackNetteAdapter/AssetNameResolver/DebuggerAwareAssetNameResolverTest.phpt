<?php

declare(strict_types=1);

namespace OopsTests\WebpackNetteAdapter\AssetNameResolver;

use Oops\WebpackNetteAdapter\AssetNameResolver\DebuggerAwareAssetNameResolver;
use Oops\WebpackNetteAdapter\AssetNameResolver\IdentityAssetNameResolver;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
final class DebuggerAwareAssetNameResolverTest extends TestCase
{
	public function testResolver(): void
	{
		$resolver = new DebuggerAwareAssetNameResolver(new IdentityAssetNameResolver());
		$resolver->resolveAssetName('asset.js');
		$resolver->resolveAssetName('asset.css');

		Assert::same([
			['asset.js', 'asset.js'],
			['asset.css', 'asset.css'],
		], $resolver->getResolvedAssets());
	}
}

(new DebuggerAwareAssetNameResolverTest())->run();
