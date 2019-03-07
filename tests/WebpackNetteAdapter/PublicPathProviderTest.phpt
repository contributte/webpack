<?php

declare(strict_types = 1);

namespace OopsTests\WebpackNetteAdapter;

use Oops\WebpackNetteAdapter\BasePath\BasePathProvider;
use Oops\WebpackNetteAdapter\DevServer;
use Oops\WebpackNetteAdapter\PublicPathProvider;
use Tester\Assert;
use Tester\TestCase;


require_once __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 */
class PublicPathProviderTest extends TestCase
{

	public function testWithDevServer(): void
	{
		$basePathProvider = \Mockery::mock(BasePathProvider::class);
		$basePathProvider->shouldReceive('getBasePath')->never();

		$devServer = \Mockery::mock(DevServer::class);
		$devServer->shouldReceive('isAvailable')->andReturn(TRUE);
		$devServer->shouldReceive('getUrl')->andReturn('http://localhost:3000');

		$provider = new PublicPathProvider('dist/', $basePathProvider, $devServer);
		Assert::same('http://localhost:3000', $provider->getPublicPath());
	}


	public function testWithoutDevServer(): void
	{
		$basePathProvider = \Mockery::mock(BasePathProvider::class);
		$basePathProvider->shouldReceive('getBasePath')->andReturn('/');

		$devServer = \Mockery::mock(DevServer::class);
		$devServer->shouldReceive('isAvailable')->andReturn(FALSE);
		$devServer->shouldReceive('getUrl')->never();

		$provider = new PublicPathProvider('dist/', $basePathProvider, $devServer);
		Assert::same('/dist', $provider->getPublicPath());
	}


	protected function tearDown(): void
	{
		parent::tearDown();
		\Mockery::close();
	}

}


(new PublicPathProviderTest())->run();
