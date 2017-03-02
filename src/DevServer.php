<?php

declare(strict_types = 1);

namespace Oops\WebpackNetteAdapter;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Tracy\Debugger;


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
	 * @var ClientInterface
	 */
	private $httpClient;


	public function __construct(bool $enabled, string $url, ClientInterface $httpClient)
	{
		$this->enabled = $enabled;
		$this->url = $url;
		$this->httpClient = $httpClient;
	}


	public function getUrl(): string
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
			try {
				/*
				 * X-Powered-By can produce false positives (e.g. different Node app running on the target port),
				 * but it seems to be the only reasonable solution, since the webpack-dev-server's root path is
				 * not guaranteed to produce a 200 response (can be 404 if the index file is missing).
				 */

				$response = $this->httpClient->request('GET', $this->url, ['http_errors' => FALSE, 'verify' => FALSE]);
				$this->available = $response->hasHeader('X-Powered-By') && $response->getHeader('X-Powered-By')[0] === 'Express';

			} catch (GuzzleException $e) {
				$this->available = FALSE;
			}
		}

		return $this->available;
	}

}
