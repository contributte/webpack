<?php

declare(strict_types=1);

namespace OopsTests\WebpackNetteAdapter\AssetNameResolver;

use Oops\WebpackNetteAdapter\AssetNameResolver\CannotResolveAssetNameException;
use Oops\WebpackNetteAdapter\AssetNameResolver\ManifestAssetNameResolver;
use Oops\WebpackNetteAdapter\Manifest\ManifestLoader;
use Oops\WebpackNetteAdapter\Manifest\Mapper\WebpackManifestPluginMapper;
use Tester\Assert;
use Tester\TestCase;
use function OopsTests\WebpackNetteAdapter\createBuildDirectoryProvider;

require_once __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
final class ManifestAssetNameResolverTest extends TestCase
{
	public function testResolver(): void
	{
		$manifestLoader = new ManifestLoader(
			createBuildDirectoryProvider(__DIR__),
			new WebpackManifestPluginMapper(),
		);

		$resolver = new ManifestAssetNameResolver('manifest.json', $manifestLoader);
		Assert::same('resolved.asset.js', $resolver->resolveAssetName('asset.js'));

		Assert::throws(function () use ($resolver): void {
			$resolver->resolveAssetName('unknownAsset.js');
		}, CannotResolveAssetNameException::class);
	}

	public function testCannotLoadManifest(): void
	{
		$manifestLoader = new ManifestLoader(
			// parent directory doesn't contain manifest.json => throws CannotLoadManifestException
			createBuildDirectoryProvider(__DIR__ . '/..'),
			new WebpackManifestPluginMapper(),
		);

		$resolver = new ManifestAssetNameResolver('manifest.json', $manifestLoader);

		Assert::throws(function () use ($resolver): void {
			$resolver->resolveAssetName('asset.js');
		}, CannotResolveAssetNameException::class);
	}
}

(new ManifestAssetNameResolverTest())->run();
