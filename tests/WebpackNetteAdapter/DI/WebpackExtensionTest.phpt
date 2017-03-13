<?php

declare(strict_types = 1);

namespace OopsTests\WebpackNetteAdapter\DI;

use Latte\Loaders\StringLoader;
use Nette\Bridges\ApplicationLatte\ILatteFactory;
use Nette\Configurator;
use Nette\DI\Container;
use Oops\WebpackNetteAdapter\AssetResolver\AssetResolverInterface;
use Oops\WebpackNetteAdapter\AssetResolver\DebuggerAwareAssetResolver;
use Oops\WebpackNetteAdapter\AssetResolver\IdentityAssetResolver;
use Oops\WebpackNetteAdapter\AssetResolver\ManifestAssetResolver;
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

	public function testBasic()
	{
		$container = $this->createContainer('basic');

		Assert::type(DebuggerAwareAssetResolver::class, $container->getByType(AssetResolverInterface::class));
		Assert::type(PublicPathProvider::class, $container->getByType(PublicPathProvider::class));
		Assert::type(DevServer::class, $devServer = $container->getByType(DevServer::class));

		/** @var DevServer $devServer */
		Assert::true($devServer->isEnabled());

		/** @var Bar $bar */
		$bar = $container->getByType(Bar::class);
		Assert::notSame(NULL, $bar->getPanel(WebpackPanel::class));
	}


	public function testNoDebug()
	{
		$container = $this->createContainer('noDebug');

		Assert::type(IdentityAssetResolver::class, $container->getByType(AssetResolverInterface::class));
		Assert::type(PublicPathProvider::class, $container->getByType(PublicPathProvider::class));
		Assert::type(DevServer::class, $devServer = $container->getByType(DevServer::class));

		/** @var DevServer $devServer */
		Assert::false($devServer->isEnabled());

		/** @var Bar $bar */
		$bar = $container->getByType(Bar::class);
		Assert::null($bar->getPanel(WebpackPanel::class));
	}


	public function testMissingRequiredFields()
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


	public function testManifestResolver()
	{
		$container = $this->createContainer('manifest');
		Assert::type(ManifestAssetResolver::class, $container->getByType(AssetResolverInterface::class));
	}


	public function testLatte()
	{
		$container = $this->createContainer('noDebug');

		/** @var ILatteFactory $latteFactory */
		$latteFactory = $container->getByType(ILatteFactory::class);
		$latte = $latteFactory->create();

		$providers = $latte->getProviders();
		Assert::type(AssetResolverInterface::class, $providers['webpackAssetResolver']);
		Assert::type(PublicPathProvider::class, $providers['webpackPublicPathProvider']);

		$latte->setLoader(new StringLoader());
		Assert::same('/dist/asset.js', $latte->renderToString('{webpack asset.js}'));
	}


	private function createContainer(string $configFile): Container
	{
		$configurator = new Configurator();
		$configurator->setTempDirectory(TEMP_DIR);
		$configurator->setDebugMode(FALSE);
		$configurator->addConfig(__DIR__ . '/config/common.neon');
		$configurator->addConfig(__DIR__ . '/config/' . $configFile . '.neon');

		return $configurator->createContainer();
	}

}


(new WebpackExtensionTest())->run();
