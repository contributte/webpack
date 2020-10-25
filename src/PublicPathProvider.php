<?php

declare(strict_types=1);

namespace Oops\WebpackNetteAdapter;

use Oops\WebpackNetteAdapter\BasePath\BasePathProvider;
use Oops\WebpackNetteAdapter\DevServer\DevServer;

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
