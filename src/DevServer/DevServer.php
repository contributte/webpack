<?php

declare(strict_types = 1);

namespace Oops\WebpackNetteAdapter\DevServer;

use Oops\WebpackNetteAdapter\DevServer\Http\Client;


class DevServer
{

	/**
	 * @var bool
	 */
	private $enabled;

	/**
	 * @var bool
	 */
	private $available;

	/**
	 * @var string
	 */
	private $url;

	/**
	 * @var ?string
	 */
	private $publicUrl;

	/**
	 * @var float
	 */
	private $timeout;

	/**
	 * @var Client
	 */
	private $httpClient;


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
		if ( ! $this->isEnabled()) {
			return FALSE;
		}

		if ($this->available === NULL) {
			$this->available = $this->httpClient->isAvailable($this->url, $this->timeout);
		}

		return $this->available;
	}

}
