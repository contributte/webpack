<?php

declare(strict_types=1);

namespace Contributte\Webpack\Tests;

use Contributte\Webpack\BuildDirectoryProvider;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/bootstrap.php';

/**
 * @testCase
 */
final class BuildDirectoryProviderTest extends TestCase
{
	public function testWithDevServer(): void
	{
		$devServer = createEnabledDevServer(true);

		$provider = new BuildDirectoryProvider('dist/', $devServer);
		Assert::same('http://webpack-dev-server:3000', $provider->getBuildDirectory());
	}

	public function testWithoutDevServer(): void
	{
		$devServer = createEnabledDevServer(false);

		$provider = new BuildDirectoryProvider('dist/', $devServer);
		Assert::same('dist/', $provider->getBuildDirectory());
	}
}

(new BuildDirectoryProviderTest())->run();
