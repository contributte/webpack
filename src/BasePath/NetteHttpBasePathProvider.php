<?php

declare(strict_types=1);

namespace Contributte\Webpack\BasePath;

use Nette\Http\IRequest;

final class NetteHttpBasePathProvider implements BasePathProvider
{
	public function __construct(
		private readonly IRequest $httpRequest,
	) {
	}

	public function getBasePath(): string
	{
		return $this->httpRequest->getUrl()->getBasePath();
	}
}
