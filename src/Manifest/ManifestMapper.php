<?php

declare(strict_types = 1);

namespace Oops\WebpackNetteAdapter\Manifest;

interface ManifestMapper
{
	public function __construct();

	/**
	 * Modifies manifest contents
	 * @param array<mixed> $manifest
	 * @return array<string, string>
	 */
	public function map(array $manifest) : array;
}