<?php

declare(strict_types=1);

namespace Contributte\Webpack\DevServer\Http;

/**
 * @internal
 */
final class MockClient implements Client
{
	private bool $isAvailable;

	public function __construct(bool $isAvailable)
	{
		$this->isAvailable = $isAvailable;
	}

	public function isAvailable(string $url, float $timeout): bool
	{
		return $this->isAvailable;
	}
}
