<?php

declare(strict_types=1);

namespace Oops\WebpackNetteAdapter\Manifest;

/**
 * Default identity mapper
 */
class IdentityMapper implements ManifestMapper
{
	public function __construct()
	{
	}

	public function map(array $manifest): array
	{
		return $manifest;
	}
}