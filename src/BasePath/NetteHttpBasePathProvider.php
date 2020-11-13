<?php

declare(strict_types=1);

namespace Contributte\Webpack\BasePath;

use Nette\Http\IRequest;

final class NetteHttpBasePathProvider implements BasePathProvider
{
	private IRequest $httpRequest;

	public function __construct(IRequest $httpRequest)
	{
		$this->httpRequest = $httpRequest;
	}

	public function getBasePath(): string
	{
		return $this->httpRequest->getUrl()->getBasePath();
	}
}
