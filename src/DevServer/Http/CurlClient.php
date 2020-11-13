<?php

declare(strict_types=1);

namespace Contributte\Webpack\DevServer\Http;

/**
 * @internal
 */
final class CurlClient implements Client
{
	public function isAvailable(string $url, float $timeout): bool
	{
		/*
		 * This can produce false positives (if a different application is listening on the target port),
		 * but I currently fail to see a better solution. The root path is not guaranteed to produce a 200 OK
		 * response (can be 404 if the index file is missing), and while webpack-dev-server at least responds
		 * with an "X-Powered-By: Express" header, webpack-serve gives no hint whatsoever.
		 */

		$curl = \curl_init($url);
		if ($curl === false) {
			return false;
		}

		\curl_setopt_array($curl, [
			\CURLOPT_CUSTOMREQUEST => 'GET',
			\CURLOPT_PROTOCOLS => \CURLPROTO_HTTP | \CURLPROTO_HTTPS,

			// no output please
			\CURLOPT_RETURNTRANSFER => false,
			\CURLOPT_HEADER => false,
			\CURLOPT_FILE => \fopen('php://temp', 'w+'),

			// setup timeout; this requires NOSIGNAL for values below 1s
			\CURLOPT_TIMEOUT_MS => $timeout * 1000,
			\CURLOPT_NOSIGNAL => $timeout < 1 && \PHP_OS_FAMILY !== 'Windows',

			// allow self-signed certificates
			\CURLOPT_SSL_VERIFYHOST => 0,
			\CURLOPT_SSL_VERIFYPEER => false,
		]);

		\curl_exec($curl);
		$error = \curl_error($curl);

		\curl_close($curl);
		return $error === '';
	}
}
