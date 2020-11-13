<?php

declare(strict_types=1);

namespace Contributte\Webpack\DevServer\Http;

interface Client
{
	public function isAvailable(string $url, float $timeout): bool;
}
