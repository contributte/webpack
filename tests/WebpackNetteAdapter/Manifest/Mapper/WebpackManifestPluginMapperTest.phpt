<?php

declare(strict_types = 1);

namespace OopsTests\WebpackNetteAdapter\Manifest\Mapper;

use Oops\WebpackNetteAdapter\Manifest\Mapper\WebpackManifestPluginMapper;
use Tester\Assert;
use Tester\TestCase;


require_once __DIR__ . '/../../../bootstrap.php';


/**
 * @testCase
 */
class WebpackManifestPluginMapperTest extends TestCase
{

	public function testMapper(): void
	{
		$mapper = new WebpackManifestPluginMapper();

		$result = $mapper->map([
			'asset.js' => 'resolved.asset.js',
		]);

		Assert::same([
			'asset.js' => 'resolved.asset.js',
		], $result);
	}

}


(new WebpackManifestPluginMapperTest())->run();
