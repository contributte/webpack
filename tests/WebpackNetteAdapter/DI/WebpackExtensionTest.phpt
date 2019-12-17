<?php

declare(strict_types = 1);

namespace OopsTests\WebpackNetteAdapter\DI;

use Latte\Loaders\StringLoader;
use Nette\Bridges\ApplicationLatte\ILatteFactory;
use Nette\Configurator;
use Nette\DI\Container;
use Oops\WebpackNetteAdapter\AssetLocator;
use Oops\WebpackNetteAdapter\AssetNameResolver\AssetNameResolverInterface;
use Oops\WebpackNetteAdapter\AssetNameResolver\DebuggerAwareAssetNameResolver;
use Oops\WebpackNetteAdapter\AssetNameResolver\IdentityAssetNameResolver;
use Oops\WebpackNetteAdapter\AssetNameResolver\ManifestAssetNameResolver;
use Oops\WebpackNetteAdapter\AssetNameResolver\StaticAssetNameResolver;
use Oops\WebpackNetteAdapter\Debugging\WebpackPanel;
use Oops\WebpackNetteAdapter\DevServer;
use Oops\WebpackNetteAdapter\DI\ConfigurationException;
use Oops\WebpackNetteAdapter\PublicPathProvider;
use Tester\Assert;
use Tester\TestCase;
use Tracy\Bar;


require_once __DIR__ . '/../../bootstrap.php';


/**
 * @testCase
 */
class WebpackExtensionTest extends TestCase
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
		Assert::notSame(NULL, $bar->getPanel(WebpackPanel::class));
	}


	public function testNoDebug(): void
	{
		$container = $this->createContainer('noDebug');

		Assert::type(IdentityAssetNameResolver::class, $container->getByType(AssetNameResolverInterface::class));
		Assert::type(PublicPathProvider::class, $container->getByType(PublicPathProvider::class));
		Assert::type(DevServer::class, $devServer = $container->getByType(DevServer::class));

		/** @var DevServer $devServer */
		Assert::false($devServer->isEnabled());

		/** @var Bar $bar */
		$bar = $container->getByType(Bar::class);
		Assert::null($bar->getPanel(WebpackPanel::class));
	}


	public function testMissingRequiredFields(): void
	{
		Assert::throws(function () {
			$this->createContainer('missingBuildDirectory');
		}, ConfigurationException::class, 'You need to specify the build directory.');

		Assert::throws(function () {
			$this->createContainer('missingBuildPublicPath');
		}, ConfigurationException::class, 'You need to specify the build public path.');

		Assert::throws(function () {
			$this->createContainer('missingDevServerUrl');
		}, ConfigurationException::class, 'You need to specify the dev server URL.');
	}


	public function testManifestResolver(): void
	{
		$container = $this->createContainer('manifest');
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
		$cache->setAccessible(TRUE);

		Assert::same(["asset.js" => "cached.resolved.asset.js"], $cache->getValue($resolver));
	}


	public function testIgnoredAssets(): void
	{
		$container = $this->createContainer('ignoredAssets');

		// mock devServer so that it is available
		$devServerMock = \Mockery::mock(DevServer::class);
		$devServerMock->shouldReceive('getUrl')->andReturn('/devServer/');
		$devServerMock->shouldReceive('isAvailable')->andReturn(TRUE);
		$container->removeService('webpack.devServer');
		$container->addService('webpack.devServer', $devServerMock);

		/** @var AssetLocator $assetLocator */
		$assetLocator = $container->getByType(AssetLocator::class);
		Assert::same('/devServer/foo.js', $assetLocator->locateInPublicPath('foo.js'));
		Assert::same('/devServer/foo.js', $assetLocator->locateInBuildDirectory('foo.js'));
		Assert::same('data:,', $assetLocator->locateInPublicPath('foo.css'));
		Assert::same('data:,', $assetLocator->locateInBuildDirectory('foo.css'));

		\Mockery::close();
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
		$configurator->setDebugMode(FALSE);

		$configurator->addParameters(['buildDir' => __DIR__]);
		$configurator->addConfig(__DIR__ . '/config/common.neon');
		$configurator->addConfig(__DIR__ . '/config/' . $configFile . '.neon');

		return $configurator->createContainer();
	}

}


(new WebpackExtensionTest())->run();
