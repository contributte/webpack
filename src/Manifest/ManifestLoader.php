<?php declare(strict_types = 1);

namespace Contributte\Webpack\Manifest;

use Contributte\Webpack\BuildDirectoryProvider;
use function curl_error;
use function curl_exec;
use function curl_init;
use function curl_setopt_array;
use function error_get_last;
use function file_get_contents;
use function is_file;
use function json_decode;
use function sprintf;
use const CURLOPT_CUSTOMREQUEST;
use const CURLOPT_FAILONERROR;
use const CURLOPT_NOSIGNAL;
use const CURLOPT_PROTOCOLS;
use const CURLOPT_RETURNTRANSFER;
use const CURLOPT_SSL_VERIFYHOST;
use const CURLOPT_SSL_VERIFYPEER;
use const CURLOPT_TIMEOUT_MS;
use const CURLPROTO_HTTP;
use const CURLPROTO_HTTPS;
use const JSON_OBJECT_AS_ARRAY;
use const JSON_THROW_ON_ERROR;
use const PHP_OS_FAMILY;

/**
 * @internal
 */
final class ManifestLoader
{

	public function __construct(
		private readonly BuildDirectoryProvider $directoryProvider,
		private readonly ManifestMapper $manifestMapper,
		private readonly float $timeout
	)
	{
	}

	/**
	 * @throws CannotLoadManifestException
	 * @return array<string, string>
	 */
	public function loadManifest(string $fileName): array
	{
		$path = $this->getManifestPath($fileName);

		if (is_file($path)) {
			$manifest = @file_get_contents($path); // @ - errors handled by custom exception
		} else {
			$ch = curl_init($path);

			if ($ch === false) {
				$manifest = false;
			} else {
				curl_setopt_array($ch, [
					CURLOPT_CUSTOMREQUEST => 'GET',
					CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,

					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_FAILONERROR => true,

					// setup timeout; this requires NOSIGNAL for values below 1s
					CURLOPT_TIMEOUT_MS => (int) $this->timeout * 1000,
					CURLOPT_NOSIGNAL => $this->timeout < 1 && PHP_OS_FAMILY !== 'Windows',

					// allow self-signed certificates
					CURLOPT_SSL_VERIFYHOST => 0,
					CURLOPT_SSL_VERIFYPEER => false,
				]);
				/** @var string|false $manifest */
				$manifest = curl_exec($ch);

				if ($manifest === false) {
					$errorMessage = curl_error($ch);
				}
			}
		}

		if ($manifest === false) {
			throw new CannotLoadManifestException(sprintf(
				"Manifest file '%s' could not be loaded: %s",
				$path,
				$errorMessage ?? error_get_last()['message'] ?? 'unknown error',
			));
		}

		return $this->manifestMapper->map(json_decode($manifest, flags: JSON_THROW_ON_ERROR | JSON_OBJECT_AS_ARRAY));
	}

	public function getManifestPath(string $fileName): string
	{
		return $this->directoryProvider->getBuildDirectory() . '/' . $fileName;
	}

}
