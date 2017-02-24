<?php

declare(strict_types = 1);

namespace Oops\WebpackNetteAdapter\DI;

use GuzzleHttp\Client;
use Latte;
use Nette\Bridges\ApplicationLatte\ILatteFactory;
use Nette\DI\CompilerExtension;
use Nette\DI\MissingServiceException;
use Nette\DI\Statement;
use Nette\Utils\Validators;
use Oops\WebpackNetteAdapter\AssetResolver;
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
			'url' => 'http://localhost:3000',
		],
		'build' => [
			'directory' => '',
			'publicPath' => '',
		],
		'assetResolver' => AssetResolver\IdentityAssetResolver::class,
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

		Validators::assertField($config['build'], 'directory', 'string:1..');
		Validators::assertField($config['build'], 'publicPath', 'string:1..');

		$builder->addDefinition($this->prefix('pathProvider'))
			->setClass(PublicPathProvider::class, [$config['build']['publicPath']]);

		$builder->addDefinition($this->prefix('buildDirProvider'))
			->setClass(BuildDirectoryProvider::class, [$config['build']['directory']]);

		$builder->addDefinition($this->prefix('devServer'))
			->setClass(DevServer::class)
			->setArguments([
				$config['devServer']['enabled'],
				$config['devServer']['url'],
				new Statement(Client::class)
			]);

		$assetResolver = $builder->addDefinition($this->prefix('assetResolver'))
			->setClass(AssetResolver\AssetResolverInterface::class)
			->setFactory($config['assetResolver']);

		if ($this->debugMode && $config['debugger']) {
			$assetResolver->setAutowired(FALSE);
			$assetResolver = $builder->addDefinition($this->prefix('assetResolver.debug'))
				->setClass(AssetResolver\DebuggerAwareAssetResolver::class, [$assetResolver]);
		}

		// latte macro
		if ($config['macros']) {
			try {
				$builder->getDefinitionByType(ILatteFactory::class)
					->addSetup('?->addProvider(?, ?)', ['@self', 'webpackAssetResolver', $assetResolver])
					->addSetup('?->addProvider(?, ?)', ['@self', 'webpackPublicPathProvider', $this->prefix('@pathProvider')])
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
