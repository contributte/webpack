<?php

declare(strict_types=1);

namespace Contributte\Webpack\Tests\DI;

use Contributte\Webpack\AssetLocator;
use Contributte\Webpack\AssetNameResolver\AssetNameResolverInterface;
use Contributte\Webpack\AssetNameResolver\DebuggerAwareAssetNameResolver;
use Contributte\Webpack\AssetNameResolver\IdentityAssetNameResolver;
use Contributte\Webpack\AssetNameResolver\ManifestAssetNameResolver;
use Contributte\Webpack\AssetNameResolver\StaticAssetNameResolver;
use Contributte\Webpack\Debugging\WebpackPanel;
use Contributte\Webpack\DevServer\DevServer;
use Contributte\Webpack\PublicPathProvider;
use Latte\Loaders\StringLoader;
use Nette\Bridges\ApplicationLatte\ILatteFactory;
use Nette\Configurator;
use Nette\DI\Container;
use Nette\DI\InvalidConfigurationException;
use Tester\Assert;
use Tester\TestCase;
use Tracy\Bar;
use function Contributte\Webpack\Tests\createEnabledDevServer;

require_once __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
final class WebpackExtensionTest extends TestCase
{
	public function testBasic(): void
	{
		$container = $this->createContainer('basic');

		Assert::type(DebuggerAwareAssetNameResolver::class, $container->getByType(AssetNameResolverInterface::class));
		Assert::type(PublicPathProvider::class, $container->getByType(PublicPathProvider::class));
		Assert::type(DevServer::class, $devServer = $container->getByType(DevServer::class));

		/** @var DevServer $devServer */
		Assert::true($devServer->isEnabled());

		/** @var Bar $bar */
		$bar = $container->getByType(Bar::class);
		Assert::notSame(null, $bar->getPanel(WebpackPanel::class));
	}

	public function testNoDebug(): void
	{
		$container = $this->createContainer('noDebug');

		Assert::type(IdentityAssetNameResolver::class, $container->getByType(AssetNameResolverInterface::class));
		Assert::type(PublicPathProvider::class, $container->getByType(PublicPathProvider::class));
		Assert::type(DevServer::class, $devServer = $container->getByType(DevServer::class));

		/** @var \Contributte\Webpack\DevServer\DevServer $devServer */
		Assert::false($devServer->isEnabled());

		/** @var Bar $bar */
		$bar = $container->getByType(Bar::class);
		Assert::null($bar->getPanel(WebpackPanel::class));
	}

	public function testMissingRequiredFields(): void
	{
		Assert::throws(function (): void {
			$this->createContainer('missingBuildDirectory');
		}, InvalidConfigurationException::class);

		Assert::throws(function (): void {
			$this->createContainer('missingBuildPublicPath');
		}, InvalidConfigurationException::class);

		Assert::throws(function (): void {
			$this->createContainer('missingDevServerUrl');
		}, InvalidConfigurationException::class);
	}

	public function testManifestResolver(): void
	{
		$container = $this->createContainer('manifest');
		Assert::type(ManifestAssetNameResolver::class, $container->getByType(AssetNameResolverInterface::class));
	}

	public function testManifestResolverWithMapper(): void
	{
		$container = $this->createContainer('manifestWithMapper');
		Assert::type(ManifestAssetNameResolver::class, $container->getByType(AssetNameResolverInterface::class));
	}

	public function testOptimizedManifest(): void
	{
		\putenv('CONTRIBUTTE_WEBPACK_OPTIMIZE_MANIFEST=1');
		$container = $this->createContainer('optimizedManifest');
		$resolver = $container->getByType(AssetNameResolverInterface::class);

		Assert::type(StaticAssetNameResolver::class, $resolver);

		$refl = new \ReflectionClass($resolver);
		$cache = $refl->getProperty('resolutions');
		$cache->setAccessible(true);

		Assert::same(['asset.js' => 'cached.resolved.asset.js'], $cache->getValue($resolver));
	}

	public function testIgnoredAssets(): void
	{
		$container = $this->createContainer('ignoredAssets');

		// mock devServer so that it is available
		$devServerMock = createEnabledDevServer(true);
		$container->removeService('webpack.devServer');
		$container->addService('webpack.devServer', $devServerMock);

		/** @var AssetLocator $assetLocator */
		$assetLocator = $container->getByType(AssetLocator::class);
		Assert::same('http://localhost:3000/foo.js', $assetLocator->locateInPublicPath('foo.js'));
		Assert::same('http://webpack-dev-server:3000/foo.js', $assetLocator->locateInBuildDirectory('foo.js'));
		Assert::same('data:,', $assetLocator->locateInPublicPath('foo.css'));
		Assert::same('data:,', $assetLocator->locateInBuildDirectory('foo.css'));
	}

	public function testLatte(): void
	{
		$container = $this->createContainer('noDebug');

		/** @var ILatteFactory $latteFactory */
		$latteFactory = $container->getByType(ILatteFactory::class);
		$latte = $latteFactory->create();

		$providers = $latte->getProviders();
		Assert::type(AssetLocator::class, $providers['webpackAssetLocator']);

		$latte->setLoader(new StringLoader());
		Assert::same('/dist/asset.js', $latte->renderToString('{webpack asset.js}'));
	}

	private function createContainer(string $configFile): Container
	{
		$configurator = new Configurator();
		$configurator->setTempDirectory(TEMP_DIR);
		$configurator->setDebugMode(false);

		$configurator->addParameters(['buildDir' => __DIR__]);
		$configurator->addConfig(__DIR__ . '/config/common.neon');
		$configurator->addConfig(__DIR__ . '/config/' . $configFile . '.neon');

		return $configurator->createContainer();
	}
}

(new WebpackExtensionTest())->run();
