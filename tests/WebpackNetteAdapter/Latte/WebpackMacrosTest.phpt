<?php

declare(strict_types = 1);

namespace OopsTests\WebpackNetteAdapter\Latte;

use Latte\Engine;
use Latte\Loaders\StringLoader;
use Oops\WebpackNetteAdapter\AssetResolver\IdentityAssetResolver;
use Oops\WebpackNetteAdapter\Latte\WebpackMacros;
use Oops\WebpackNetteAdapter\PublicPathProvider;
use Tester\Assert;
use Tester\TestCase;


require_once __DIR__ . '/../../bootstrap.php';


/**
 * @testCase
 */
class WebpackMacrosTest extends TestCase
{

	public function testMacros()
	{
		$latte = new Engine();
		$latte->addProvider('webpackAssetResolver', new IdentityAssetResolver());

		$pathProvider = \Mockery::mock(PublicPathProvider::class);
		$pathProvider->shouldReceive('getPath')->andReturn('/dist');
		$latte->addProvider('webpackPublicPathProvider', $pathProvider);

		$latte->onCompile[] = function (Engine $engine) {
			WebpackMacros::install($engine->getCompiler());
		};

		$latte->setLoader(new StringLoader());
		Assert::same('/dist/asset.js', $latte->renderToString('{webpack asset.js}'));
	}

}


(new WebpackMacrosTest())->run();
