<?php

declare(strict_types=1);

namespace Contributte\Webpack;

use Contributte\Webpack\BasePath\BasePathProvider;
use Contributte\Webpack\DevServer\DevServer;

/**
 * @internal
 */
final class PublicPathProvider
{
	private string $path;

	private BasePathProvider $basePathProvider;

	private DevServer $devServer;

	public function __construct(string $path, BasePathProvider $basePathProvider, DevServer $devServer)
	{
		$this->path = $path;
		$this->basePathProvider = $basePathProvider;
		$this->devServer = $devServer;
	}

	public function getPublicPath(): string
	{
		return $this->devServer->isAvailable()
			? $this->devServer->getUrl()
			: \rtrim($this->basePathProvider->getBasePath(), '/') . '/' . \trim($this->path, '/');
	}
}
