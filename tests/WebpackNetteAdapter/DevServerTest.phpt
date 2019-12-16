<?php

declare(strict_types = 1);

namespace OopsTests\WebpackNetteAdapter;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mockery\MockInterface;
use Oops\WebpackNetteAdapter\DevServer;
use Tester\Assert;
use Tester\TestCase;


require_once __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 */
class DevServerTest extends TestCase
{

	/**
	 * @var MockInterface|Client
	 */
	private $httpClient;


	protected function setUp(): void
	{
		parent::setUp();
		$this->httpClient = \Mockery::mock(Client::class);
	}


	public function testDevServer(): void
	{
		$devServer = new DevServer(TRUE, 'http://localhost:3000', '', 0.1, $this->httpClient);
		Assert::true($devServer->isEnabled());

		$this->httpClient->shouldReceive('request')
			->with('GET', 'http://localhost:3000', ['http_errors' => FALSE, 'verify' => FALSE, 'timeout' => 0.1])
			->andReturn(new Response(404));
		Assert::true($devServer->isAvailable());
	}


	public function testPublicUrl(): void
	{
	    $devServer = new DevServer(TRUE, 'http://localhost:3000', 'http://localhost:3030', 0.1, $this->httpClient);
	    Assert::true($devServer->isEnabled());
	    Assert::same($devServer->getUrl(), 'http://localhost:3030');

	    $this->httpClient->shouldReceive('request')
	        ->with('GET', 'http://localhost:3000', ['http_errors' => FALSE, 'verify' => FALSE, 'timeout' => 0.1])
	        ->andReturn(new Response(404));

        Assert::true($devServer->isAvailable());
	}


	public function testUnavailable(): void
	{
		$devServer = new DevServer(TRUE, 'http://localhost:3000', '', 0.5, $this->httpClient);
		Assert::true($devServer->isEnabled());

		$this->httpClient->shouldReceive('request')
			->with('GET', 'http://localhost:3000', ['http_errors' => FALSE, 'verify' => FALSE, 'timeout' => 0.5])
			->andThrow(new RequestException('', new Request('GET', 'http://localhost:3000')));
		Assert::false($devServer->isAvailable());
	}


	public function testDisabled(): void
	{
		$devServer = new DevServer(FALSE, 'http://localhost:3000', '', 0.1, $this->httpClient);
		Assert::false($devServer->isEnabled());
		Assert::false($devServer->isAvailable());
	}


	protected function tearDown(): void
	{
		parent::tearDown();
		\Mockery::close();
	}

}


(new DevServerTest())->run();
