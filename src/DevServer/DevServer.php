<?php

declare(strict_types=1);

namespace Contributte\Webpack\DevServer;

use Contributte\Webpack\DevServer\Http\Client;

final class DevServer
{
	private bool $enabled;

	private ?bool $available = null;

	private string $url;

	private ?string $publicUrl;

	private float $timeout;

	private Client $httpClient;

	public function __construct(bool $enabled, string $url, ?string $publicUrl, float $timeout, Client $httpClient)
	{
		$this->enabled = $enabled;
		$this->url = $url;
		$this->publicUrl = $publicUrl;
		$this->timeout = $timeout;
		$this->httpClient = $httpClient;
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

		if ($this->available === null) {
			$this->available = $this->httpClient->isAvailable($this->url, $this->timeout);
		}

		return $this->available;
	}
}
