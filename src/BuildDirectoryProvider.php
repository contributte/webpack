<?php

declare(strict_types=1);

namespace Contributte\Webpack;

use Contributte\Webpack\DevServer\DevServer;

/**
 * @internal
 */
final class BuildDirectoryProvider
{
	public function __construct(
		private readonly string $directory,
		private readonly DevServer $devServer,
	) {
	}

	public function getBuildDirectory(): string
	{
		return $this->devServer->isAvailable()
			? $this->devServer->getInternalUrl()
			: $this->directory;
	}
}
