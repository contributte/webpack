<?php

declare(strict_types = 1);

namespace Oops\WebpackNetteAdapter;

use Oops\WebpackNetteAdapter\AssetNameResolver\AssetNameResolverInterface;


class AssetLocator
{

	/**
	 * @var BuildDirectoryProvider
	 */
	private $directoryProvider;

	/**
	 * @var PublicPathProvider
	 */
	private $publicPathProvider;

	/**
	 * @var AssetNameResolverInterface
	 */
	private $assetResolver;


	public function __construct(
		BuildDirectoryProvider $directoryProvider,
		PublicPathProvider $publicPathProvider,
		AssetNameResolverInterface $assetResolver
	)
	{
		$this->directoryProvider = $directoryProvider;
		$this->publicPathProvider = $publicPathProvider;
		$this->assetResolver = $assetResolver;
	}


	public function locateInPublicPath(string $asset): string
	{
		return \rtrim($this->publicPathProvider->getPublicPath(), '/') . '/' . \ltrim($this->assetResolver->resolveAssetName($asset), '/');
	}


	public function locateInBuildDirectory(string $asset): string
	{
		return \rtrim($this->directoryProvider->getBuildDirectory(), '/') . '/' . \ltrim($this->assetResolver->resolveAssetName($asset), '/');
	}

}
