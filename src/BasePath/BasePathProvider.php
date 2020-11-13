<?php

declare(strict_types=1);

namespace Contributte\Webpack\BasePath;

interface BasePathProvider
{
	public function getBasePath(): string;
}
