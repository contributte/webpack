<?php

declare(strict_types=1);

namespace Contributte\Webpack\Tests\Manifest\Mapper;

use Contributte\Webpack\Manifest\Mapper\WebpackManifestPluginMapper;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
final class WebpackManifestPluginMapperTest extends TestCase
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
