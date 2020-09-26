<?php

declare(strict_types=1);

namespace Oops\WebpackNetteAdapter\DevServer\Http;


interface Client
{
	public function isAvailable(string $url, float $timeout): bool;
}
