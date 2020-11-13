<?php

declare(strict_types=1);

namespace Contributte\Webpack\Manifest\Mapper;

use Contributte\Webpack\Manifest\ManifestMapper;

/**
 * Default identity mapper compatible with webpack-manifest-mapper's flat structure.
 */
final class WebpackManifestPluginMapper extends ManifestMapper
{
	public function map(array $manifest): array
	{
		return $manifest;
	}
}
