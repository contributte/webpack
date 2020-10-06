<?php

declare(strict_types=1);

namespace OopsTests\WebpackNetteAdapter;

use Oops\WebpackNetteAdapter\DevServer\DevServer;
use Oops\WebpackNetteAdapter\DevServer\Http\MockClient;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 */
class DevServerTest extends TestCase
{
	public function testDevServer(): void
	{
		$devServer = new DevServer(true, 'http://localhost:3000', null, 0.1, new MockClient(true));
		Assert::true($devServer->isEnabled());
		Assert::same($devServer->getUrl(), 'http://localhost:3000');
		Assert::same($devServer->getInternalUrl(), 'http://localhost:3000');
		Assert::true($devServer->isAvailable());
	}

	public function testPublicUrl(): void
	{
		$devServer = new DevServer(true, 'http://localhost:3000', 'http://localhost:3030', 0.1, new MockClient(true));
		Assert::true($devServer->isEnabled());
		Assert::same($devServer->getUrl(), 'http://localhost:3030');
		Assert::same($devServer->getInternalUrl(), 'http://localhost:3000');
		Assert::true($devServer->isAvailable());
	}

	public function testUnavailable(): void
	{
		$devServer = new DevServer(true, 'http://localhost:3000', null, 0.5, new MockClient(false));
		Assert::true($devServer->isEnabled());
		Assert::false($devServer->isAvailable());
	}

	public function testDisabled(): void
	{
		$devServer = new DevServer(false, 'http://localhost:3000', null, 0.1, new MockClient(true));
		Assert::false($devServer->isEnabled());
		Assert::false($devServer->isAvailable());
	}
}


(new DevServerTest())->run();
