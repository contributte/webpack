<?php

declare(strict_types=1);

namespace OopsTests\WebpackNetteAdapter\Latte;

use Latte\Engine;
use Latte\Loaders\StringLoader;
use Oops\WebpackNetteAdapter\AssetLocator;
use Oops\WebpackNetteAdapter\Latte\WebpackMacros;
use Tester\Assert;
use Tester\TestCase;
use function OopsTests\WebpackNetteAdapter\createAssetNameResolver;
use function OopsTests\WebpackNetteAdapter\createBuildDirectoryProvider;
use function OopsTests\WebpackNetteAdapter\createDisabledDevServer;
use function OopsTests\WebpackNetteAdapter\createPublicPathProvider;

require_once __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
final class WebpackMacrosTest extends TestCase
{
	public function testMacros(): void
	{
		$assetLocator = new AssetLocator(
			createBuildDirectoryProvider('/home/user'),
			createPublicPathProvider('/dist'),
			createAssetNameResolver(['asset.js' => 'asset.js']),
			createDisabledDevServer(),
			[],
		);

		$latte = new Engine();
		$latte->addProvider('webpackAssetLocator', $assetLocator);
		$latte->onCompile[] = function (Engine $engine): void {
			WebpackMacros::install($engine->getCompiler());
		};

		$latte->setLoader(new StringLoader());
		Assert::same('/dist/asset.js', $latte->renderToString('{webpack asset.js}'));
	}
}

(new WebpackMacrosTest())->run();
