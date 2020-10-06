<?php

declare(strict_types=1);

namespace Oops\WebpackNetteAdapter\DevServer\Http;

/**
 * @internal
 */
final class MockClient implements Client
{
	/** @var bool */
	private $isAvailable;

	public function __construct(bool $isAvailable)
	{
		$this->isAvailable = $isAvailable;
	}

	public function isAvailable(string $url, float $timeout): bool
	{
		return $this->isAvailable;
	}
}
