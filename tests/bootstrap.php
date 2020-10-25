<?php

namespace OopsTests\WebpackNetteAdapter;

use Oops\WebpackNetteAdapter\AssetLocator;
use Oops\WebpackNetteAdapter\AssetNameResolver\AssetNameResolverInterface;
use Oops\WebpackNetteAdapter\AssetNameResolver\CannotResolveAssetNameException;
use Oops\WebpackNetteAdapter\BasePath\BasePathProvider;
use Oops\WebpackNetteAdapter\BuildDirectoryProvider;
use Oops\WebpackNetteAdapter\DevServer\DevServer;
use Oops\WebpackNetteAdapter\DevServer\Http\MockClient;
use Oops\WebpackNetteAdapter\PublicPathProvider;
use Tester\Environment;
use Tester\Helpers;

require_once __DIR__ . '/../vendor/autoload.php';

Environment::setup();
\date_default_timezone_set('Europe/Prague');
\define('TEMP_DIR', __DIR__ . '/temp/' . (isset($_SERVER['argv']) ? \md5(\serialize($_SERVER['argv'])) : \getmypid()));
Helpers::purge(TEMP_DIR);

function createBuildDirectoryProvider(string $directory): BuildDirectoryProvider
{
	return new BuildDirectoryProvider($directory, createDisabledDevServer());
}

function createBasePathProvider(string $basePath): BasePathProvider
{
	return new class($basePath) implements BasePathProvider {
		private string $basePath;

		public function __construct(string $basePath)
		{
			$this->basePath = $basePath;
		}

		public function getBasePath(): string
		{
			return $this->basePath;
		}
	};
}

function createPublicPathProvider(string $path): PublicPathProvider
{
	return new PublicPathProvider($path, createBasePathProvider('/'), createDisabledDevServer());
}

function createDisabledDevServer(): DevServer
{
	return new DevServer(false, '', null, 0.1, new MockClient(false));
}

function createEnabledDevServer(bool $isAvailable): DevServer
{
	return new DevServer(true, 'http://webpack-dev-server:3000', 'http://localhost:3000', 0.1, new MockClient($isAvailable));
}

/**
 * @param array<string, string> $mapping
 */
function createAssetNameResolver(array $mapping): AssetNameResolverInterface
{
	return new class($mapping) implements AssetNameResolverInterface {
		/** @var array<string, string> */
		private array $mapping;

		/**
		 * @param array<string, string> $mapping
		 */
		public function __construct(array $mapping)
		{
			$this->mapping = $mapping;
		}

		public function resolveAssetName(string $asset): string
		{
			if (!\array_key_exists($asset, $this->mapping)) {
				throw new CannotResolveAssetNameException();
			}

			return $this->mapping[$asset];
		}
	};
}

function createAssetLocator(): AssetLocator
{
	return new AssetLocator();
}
