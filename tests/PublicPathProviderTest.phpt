<?php

declare(strict_types=1);

namespace Contributte\Webpack\Tests;

use Contributte\Webpack\PublicPathProvider;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/bootstrap.php';

/**
 * @testCase
 */
final class PublicPathProviderTest extends TestCase
{
	public function testWithDevServer(): void
	{
		$basePathProvider = createBasePathProvider('/');
		$devServer = createEnabledDevServer(true);

		$provider = new PublicPathProvider('dist/', $basePathProvider, $devServer);
		Assert::same('http://localhost:3000', $provider->getPublicPath());
	}

	public function testWithoutDevServer(): void
	{
		$basePathProvider = createBasePathProvider('/');
		$devServer = createEnabledDevServer(false);

		$provider = new PublicPathProvider('dist/', $basePathProvider, $devServer);
		Assert::same('/dist', $provider->getPublicPath());
	}
}

(new PublicPathProviderTest())->run();
