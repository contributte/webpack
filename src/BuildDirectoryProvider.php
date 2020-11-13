<?php

declare(strict_types=1);

namespace Contributte\Webpack;

use Contributte\Webpack\DevServer\DevServer;

/**
 * @internal
 */
final class BuildDirectoryProvider
{
	private string $directory;

	private DevServer $devServer;

	public function __construct(string $directory, DevServer $devServer)
	{
		$this->directory = $directory;
		$this->devServer = $devServer;
	}

	public function getBuildDirectory(): string
	{
		return $this->devServer->isAvailable()
			? $this->devServer->getInternalUrl()
			: $this->directory;
	}
}
