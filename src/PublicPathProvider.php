<?php

declare(strict_types = 1);

namespace Oops\WebpackNetteAdapter;

use Nette\Http\IRequest;


/**
 * @internal
 */
class PublicPathProvider
{

	/**
	 * @var string
	 */
	private $path;

	/**
	 * @var IRequest
	 */
	private $httpRequest;

	/**
	 * @var DevServer
	 */
	private $devServer;


	public function __construct(string $path, IRequest $httpRequest, DevServer $devServer)
	{
		$this->path = $path;
		$this->httpRequest = $httpRequest;
		$this->devServer = $devServer;
	}


	public function getPublicPath(): string
	{
		return $this->devServer->isAvailable()
			? $this->devServer->getUrl()
			: \rtrim($this->httpRequest->getUrl()->getBasePath(), '/') . '/' . \trim($this->path, '/');
	}

}
