<?php

declare(strict_types=1);

namespace Contributte\Webpack\DevServer\Http;

/**
 * @internal
 */
final class MockClient implements Client
{
	public function __construct(
		private readonly bool $isAvailable
	) {
	}

	public function isAvailable(string $url, float $timeout): bool
	{
		return $this->isAvailable;
	}
}
