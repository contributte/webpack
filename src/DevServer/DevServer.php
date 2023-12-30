<?php

declare(strict_types=1);

namespace Contributte\Webpack\DevServer;

use Contributte\Webpack\DevServer\Http\Client;

final class DevServer
{
	private ?bool $available = null;

	public function __construct(
		private readonly bool $enabled,
		private readonly string $url,
		private readonly ?string $publicUrl,
		private readonly float $timeout,
		private readonly Client $httpClient,
	) {
	}

	public function getUrl(): string
	{
		return $this->publicUrl ?? $this->url;
	}

	public function getInternalUrl(): string
	{
		return $this->url;
	}

	public function isEnabled(): bool
	{
		return $this->enabled;
	}

	public function isAvailable(): bool
	{
		if (!$this->isEnabled()) {
			return false;
		}

		return $this->available ??= $this->httpClient->isAvailable($this->url, $this->timeout);
	}
}
