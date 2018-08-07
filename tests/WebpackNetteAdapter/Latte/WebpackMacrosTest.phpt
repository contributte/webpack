<?php

declare(strict_types = 1);

namespace OopsTests\WebpackNetteAdapter\Latte;

use Latte\Engine;
use Latte\Loaders\StringLoader;
use Oops\WebpackNetteAdapter\AssetLocator;
use Oops\WebpackNetteAdapter\Latte\WebpackMacros;
use Tester\Assert;
use Tester\TestCase;


require_once __DIR__ . '/../../bootstrap.php';


/**
 * @testCase
 */
class WebpackMacrosTest extends TestCase
{

	public function testMacros(): void
	{
		$assetLocator = \Mockery::mock(AssetLocator::class);
		$assetLocator->shouldReceive('locateInPublicPath')
			->with('asset.js')
			->andReturn('/dist/asset.js');

		$latte = new Engine();
		$latte->addProvider('webpackAssetLocator', $assetLocator);
		$latte->onCompile[] = function (Engine $engine) {
			WebpackMacros::install($engine->getCompiler());
		};

		$latte->setLoader(new StringLoader());
		Assert::same('/dist/asset.js', $latte->renderToString('{webpack asset.js}'));

		\Mockery::close();
	}

}


(new WebpackMacrosTest())->run();
