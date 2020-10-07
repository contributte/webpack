<?php

declare(strict_types=1);

namespace OopsTests\WebpackNetteAdapter\DI;

use Latte\Loaders\StringLoader;
use Nette\Bridges\ApplicationLatte\ILatteFactory;
use Nette\Configurator;
use Nette\DI\Container;
use Nette\DI\InvalidConfigurationException;
use Oops\WebpackNetteAdapter\AssetLocator;
use Oops\WebpackNetteAdapter\AssetNameResolver\AssetNameResolverInterface;
use Oops\WebpackNetteAdapter\AssetNameResolver\DebuggerAwareAssetNameResolver;
use Oops\WebpackNetteAdapter\AssetNameResolver\IdentityAssetNameResolver;
use Oops\WebpackNetteAdapter\AssetNameResolver\ManifestAssetNameResolver;
use Oops\WebpackNetteAdapter\AssetNameResolver\StaticAssetNameResolver;
use Oops\WebpackNetteAdapter\Debugging\WebpackPanel;
use Oops\WebpackNetteAdapter\DevServer\DevServer;
use Oops\WebpackNetteAdapter\PublicPathProvider;
use Tester\Assert;
use Tester\TestCase;
use Tracy\Bar;
use function OopsTests\WebpackNetteAdapter\createEnabledDevServer;

require_once __DIR__ . '/../../bootstrap.php';

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

		/** @var \Oops\WebpackNetteAdapter\DevServer\DevServer $devServer */
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
		\putenv('OOPS_WEBPACK_OPTIMIZE_MANIFEST=1');
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
