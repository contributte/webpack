<?php

declare(strict_types = 1);

namespace Oops\WebpackNetteAdapter\DI;

use GuzzleHttp\Client;
use Latte;
use Nette\Bridges\ApplicationLatte\ILatteFactory;
use Nette\DI\CompilerExtension;
use Nette\DI\MissingServiceException;
use Nette\DI\Statement;
use Oops\WebpackNetteAdapter\AssetLocator;
use Oops\WebpackNetteAdapter\AssetNameResolver;
use Oops\WebpackNetteAdapter\BuildDirectoryProvider;
use Oops\WebpackNetteAdapter\Debugging\WebpackPanel;
use Oops\WebpackNetteAdapter\DevServer;
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
		],
		'build' => [
			'directory' => NULL,
			'publicPath' => NULL,
		],
		'assetResolver' => AssetNameResolver\IdentityAssetNameResolver::class,
	];

	/**
	 * @var bool
	 */
	private $debugMode;


	public function __construct(bool $debugMode)
	{
		$this->defaults['debugger'] = interface_exists(Tracy\IBarPanel::class);
		$this->defaults['macros'] = class_exists(Latte\Engine::class);
		$this->defaults['devServer']['enabled'] = $debugMode;
		$this->debugMode = $debugMode;
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
				new Statement(Client::class)
			]);

		$assetResolver = $builder->addDefinition($this->prefix('assetResolver'))
			->setClass(AssetNameResolver\AssetNameResolverInterface::class)
			->setFactory($config['assetResolver']);

		if ($this->debugMode && $config['debugger']) {
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

		if ($this->debugMode && $this->config['debugger']) {
			$builder->getDefinition($this->prefix('pathProvider'))
				->addSetup('@Tracy\Bar::addPanel', [
					new Statement(WebpackPanel::class)
				]);
		}
	}

}
