<?php declare(strict_types = 1);

namespace Contributte\Webpack\Tests\Latte;

use Contributte\Webpack\AssetLocator;
use Contributte\Webpack\Latte\WebpackMacros;
use Latte\Engine;
use Latte\Loaders\StringLoader;
use Tester\Assert;
use Tester\Environment;
use Tester\TestCase;
use function Contributte\Webpack\Tests\createAssetNameResolver;
use function Contributte\Webpack\Tests\createBuildDirectoryProvider;
use function Contributte\Webpack\Tests\createDisabledDevServer;
use function Contributte\Webpack\Tests\createPublicPathProvider;
use function version_compare;

require_once __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
final class WebpackMacrosTest extends TestCase
{

	public function testMacros(): void
	{
		if (version_compare(Engine::VERSION, '3', '>')) {
			Environment::skip('Requires Latte 2.');
		}

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
