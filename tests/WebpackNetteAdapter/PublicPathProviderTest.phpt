<?php

declare(strict_types = 1);

namespace OopsTests\WebpackNetteAdapter;

use Nette\Http\IRequest;
use Nette\Http\UrlScript;
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

	public function testWithDevServer()
	{
		$httpRequest = \Mockery::mock(IRequest::class);
		$httpRequest->shouldReceive('getUrl')->never();

		$devServer = \Mockery::mock(DevServer::class);
		$devServer->shouldReceive('isAvailable')->andReturn(TRUE);
		$devServer->shouldReceive('getUrl')->andReturn('http://localhost:3000');

		$provider = new PublicPathProvider('dist/', $httpRequest, $devServer);
		Assert::same('http://localhost:3000', $provider->getPath());
	}


	public function testWithoutDevServer()
	{
		$httpRequest = \Mockery::mock(IRequest::class);
		$url = new UrlScript('http://example.com/');
		$httpRequest->shouldReceive('getUrl')->andReturn($url);

		$devServer = \Mockery::mock(DevServer::class);
		$devServer->shouldReceive('isAvailable')->andReturn(FALSE);
		$devServer->shouldReceive('getUrl')->never();

		$provider = new PublicPathProvider('dist/', $httpRequest, $devServer);
		Assert::same('/dist', $provider->getPath());
	}


	protected function tearDown()
	{
		parent::tearDown();
		\Mockery::close();
	}

}


(new PublicPathProviderTest())->run();
