<?php

declare(strict_types = 1);

namespace OopsTests\WebpackNetteAdapter;

use Oops\WebpackNetteAdapter\BuildDirectoryProvider;
use Oops\WebpackNetteAdapter\DevServer;
use Tester\Assert;
use Tester\TestCase;


require_once __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 */
class BuildDirectoryProviderTest extends TestCase
{

	public function testWithDevServer(): void
	{
		$devServer = \Mockery::mock(DevServer::class);
		$devServer->shouldReceive('isAvailable')->andReturn(TRUE);
		$devServer->shouldReceive('getInternalUrl')->andReturn('http://localhost:3000');

		$provider = new BuildDirectoryProvider('dist/', $devServer);
		Assert::same('http://localhost:3000', $provider->getBuildDirectory());
	}


	public function testWithoutDevServer(): void
	{
		$devServer = \Mockery::mock(DevServer::class);
		$devServer->shouldReceive('isAvailable')->andReturn(FALSE);
		$devServer->shouldReceive('getInternalUrl')->never();

		$provider = new BuildDirectoryProvider('dist/', $devServer);
		Assert::same('dist/', $provider->getBuildDirectory());
	}


	protected function tearDown(): void
	{
		parent::tearDown();
		\Mockery::close();
	}

}


(new BuildDirectoryProviderTest())->run();
