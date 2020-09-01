<?php

declare(strict_types=1);

namespace Oops\WebpackNetteAdapter\Manifest;

/**
 * Maps from https://github.com/ztoben/assets-webpack-plugin format to flat files
 *
 * from
 * {
 *   "main": {
 *     "js": "/public/path/to/asset"
 *   }
 * }
 *
 * to flat used in ManifestAssetNameResolver:
 * {
 *  "main.js": "/public/path/to/asset"
 * }
 *
 */
class AssetsWebpackPluginMapper implements ManifestMapper
{
	public function __construct()
	{
	}

	/**
	 * @inheritDoc
	 */
	public function map(array $manifest): array
	{
		$result = [];
		foreach ($manifest as $main => $parts) {
			foreach ($parts as $name => $file) {
				$result[$main . '.' . $name] = $file;
			}
		}
		return $result;
	}
}