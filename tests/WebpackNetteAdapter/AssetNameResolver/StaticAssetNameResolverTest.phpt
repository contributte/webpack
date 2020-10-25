<?php

declare(strict_types=1);

namespace OopsTests\WebpackNetteAdapter\AssetNameResolver;

use Oops\WebpackNetteAdapter\AssetNameResolver\CannotResolveAssetNameException;
use Oops\WebpackNetteAdapter\AssetNameResolver\StaticAssetNameResolver;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
final class StaticAssetNameResolverTest extends TestCase
{
	public function testResolver(): void
	{
		$resolver = new StaticAssetNameResolver([
			'asset.js' => 'cached.resolved.asset.js',
		]);

		Assert::same('cached.resolved.asset.js', $resolver->resolveAssetName('asset.js'));
	}

	public function testCannotResolveAsset(): void
	{
		$resolver = new StaticAssetNameResolver([
			'asset.js' => 'cached.resolved.asset.js',
		]);

		Assert::throws(function () use ($resolver): void {
			$resolver->resolveAssetName('unknownAsset.js');
		}, CannotResolveAssetNameException::class);
	}
}

(new StaticAssetNameResolverTest())->run();
