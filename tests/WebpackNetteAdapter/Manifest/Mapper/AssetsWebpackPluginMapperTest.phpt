<?php

declare(strict_types = 1);

namespace OopsTests\WebpackNetteAdapter\Manifest\Mapper;

use Oops\WebpackNetteAdapter\Manifest\Mapper\AssetsWebpackPluginMapper;
use Tester\Assert;
use Tester\TestCase;


require_once __DIR__ . '/../../../bootstrap.php';


/**
 * @testCase
 */
class AssetsWebpackPluginMapperTest extends TestCase
{

	public function testMapper(): void
	{
		$mapper = new AssetsWebpackPluginMapper();

		$result = $mapper->map([
			'asset' => [
				'js' => 'resolved.asset.js',
			],
		]);

		Assert::same([
			'asset.js' => 'resolved.asset.js',
		], $result);
	}

}


(new AssetsWebpackPluginMapperTest())->run();
