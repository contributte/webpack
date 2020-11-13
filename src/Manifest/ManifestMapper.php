<?php

declare(strict_types=1);

namespace Contributte\Webpack\Manifest;

abstract class ManifestMapper
{
	final public function __construct()
	{
	}

	/**
	 * Transforms manifest content to a flat map from asset names to resolved names.
	 * @param array<mixed> $manifest
	 * @return array<string, string>
	 */
	abstract public function map(array $manifest): array;
}
