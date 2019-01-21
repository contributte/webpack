<?php

declare(strict_types = 1);

namespace Oops\WebpackNetteAdapter\DI;

use GuzzleHttp\Client;
use Latte;
use Nette\Bridges\ApplicationLatte\ILatteFactory;
use Nette\DI\CompilerExtension;
use Nette\DI\MissingServiceException;
use Nette\DI\ServiceDefinition;
use Nette\DI\Statement;
use Oops\WebpackNetteAdapter\AssetLocator;
use Oops\WebpackNetteAdapter\AssetNameResolver;
use Oops\WebpackNetteAdapter\BuildDirectoryProvider;
use Oops\WebpackNetteAdapter\Debugging\WebpackPanel;
use Oops\WebpackNetteAdapter\DevServer;
use Oops\WebpackNetteAdapter\Manifest\ManifestLoader;
use Oops\WebpackNetteAdapter\PublicPathProvider;
use Tracy;


class WebpackExtension extends CompilerExtension
{

	private $defaults = [
		'debugger' => NULL,
		'macros' => NULL,
		'devServer' => [
			'enabled' => NULL,
			'url' => NULL,
            'timeout' => 0.1,
		],
		'build' => [
			'directory' => NULL,
			'publicPath' => NULL,
		],
		'manifest' => [
			'name' => NULL,
			'optimize' => NULL,
		]
	];


	public function __construct(bool $debugMode)
	{
		$this->defaults['debugger'] = $debugMode;
		$this->defaults['macros'] = class_exists(Latte\Engine::class);
		$this->defaults['devServer']['enabled'] = $debugMode;
		$this->defaults['manifest']['optimize'] = ! $debugMode;
	}


	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaults);

		if (empty($config['build']['directory'])) {
			throw new ConfigurationException('You need to specify the build directory.');
		}

		if (empty($config['build']['publicPath'])) {
			throw new ConfigurationException('You need to specify the build public path.');
		}

		if ($config['devServer']['enabled'] && empty($config['devServer']['url'])) {
			throw new ConfigurationException('You need to specify the dev server URL.');
		}


		$builder->addDefinition($this->prefix('pathProvider'))
			->setClass(PublicPathProvider::class, [$config['build']['publicPath']]);

		$builder->addDefinition($this->prefix('buildDirProvider'))
			->setClass(BuildDirectoryProvider::class, [$config['build']['directory']]);

		$assetLocator = $builder->addDefinition($this->prefix('assetLocator'))
			->setClass(AssetLocator::class);

		$builder->addDefinition($this->prefix('devServer'))
			->setClass(DevServer::class)
			->setArguments([
				$config['devServer']['enabled'],
				$config['devServer']['url'] ?? '',
				$config['devServer']['timeout'],
				new Statement(Client::class)
			]);

		$assetResolver = $this->setupAssetResolver($config);

		if ($config['debugger']) {
			$assetResolver->setAutowired(FALSE);
			$builder->addDefinition($this->prefix('assetResolver.debug'))
				->setClass(AssetNameResolver\DebuggerAwareAssetNameResolver::class, [$assetResolver]);
		}

		// latte macro
		if ($config['macros']) {
			try {
				$builder->getDefinitionByType(ILatteFactory::class)
					->addSetup('?->addProvider(?, ?)', ['@self', 'webpackAssetLocator', $assetLocator])
					->addSetup('?->onCompile[] = function ($engine) { Oops\WebpackNetteAdapter\Latte\WebpackMacros::install($engine->getCompiler()); }', ['@self']);

			} catch (MissingServiceException $e) {
				// ignore
			}
		}
	}


	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();

		if ($this->config['debugger'] && interface_exists(Tracy\IBarPanel::class)) {
			$builder->getDefinition($this->prefix('pathProvider'))
				->addSetup('@Tracy\Bar::addPanel', [
					new Statement(WebpackPanel::class)
				]);
		}
	}


	private function setupAssetResolver(array $config): ServiceDefinition
	{
		$builder = $this->getContainerBuilder();

		$assetResolver = $builder->addDefinition($this->prefix('assetResolver'))
			->setClass(AssetNameResolver\AssetNameResolverInterface::class);

		if ($config['manifest']['name'] !== NULL) {
			if ( ! $config['manifest']['optimize']) {
				$loader = $builder->addDefinition($this->prefix('manifestLoader'))
					->setClass(ManifestLoader::class)
					->setAutowired(FALSE);

				$assetResolver->setFactory(AssetNameResolver\ManifestAssetNameResolver::class, [
					$config['manifest']['name'],
					$loader
				]);

			} else {
				$devServerInstance = new DevServer(
					$config['devServer']['enabled'],
					$config['devServer']['url'] ?? '',
					$config['devServer']['timeout'] ?? 0.1,
					new Client()
				);

				$directoryProviderInstance = new BuildDirectoryProvider($config['build']['directory'], $devServerInstance);
				$loaderInstance = new ManifestLoader($directoryProviderInstance);
				$manifestCache = $loaderInstance->loadManifest($config['manifest']['name']);

				$assetResolver->setFactory(AssetNameResolver\StaticAssetNameResolver::class, [$manifestCache]);

				// add dependency so that container is recompiled if manifest changes
				$manifestPath = $loaderInstance->getManifestPath($config['manifest']['name']);
				$this->compiler->addDependencies([$manifestPath]);
			}

		} else {
			$assetResolver->setFactory(AssetNameResolver\IdentityAssetNameResolver::class);
		}

		return $assetResolver;
	}

}
