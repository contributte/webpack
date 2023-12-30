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
	public function __construct(
		private readonly string $path,
		private readonly BasePathProvider $basePathProvider,
		private readonly DevServer $devServer,
	) {
	}

	public function getPublicPath(): string
	{
		return $this->devServer->isAvailable()
			? $this->devServer->getUrl()
			: \rtrim($this->basePathProvider->getBasePath(), '/') . '/' . \trim($this->path, '/');
	}
}
