<?php

declare(strict_types=1);

namespace OopsTests\WebpackNetteAdapter\Manifest;

use Oops\WebpackNetteAdapter\Manifest\CannotLoadManifestException;
use Oops\WebpackNetteAdapter\Manifest\ManifestLoader;
use Oops\WebpackNetteAdapter\Manifest\ManifestMapper;
use Tester\Assert;
use Tester\TestCase;
use function OopsTests\WebpackNetteAdapter\createBuildDirectoryProvider;

require_once __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
final class ManifestLoaderTest extends TestCase
{
	public function testLoader(): void
	{
		$buildDirProvider = createBuildDirectoryProvider(__DIR__);

		$manifestMapper = new class() extends ManifestMapper {
			public function map(array $manifest): array
			{
				Assert::equal(['asset.js' => 'resolved.asset.js'], $manifest);
				return ['asset.js' => 'mapped.asset.js'];
			}
		};

		$manifestLoader = new ManifestLoader($buildDirProvider, $manifestMapper);

		Assert::same(__DIR__ . '/manifest.json', $manifestLoader->getManifestPath('manifest.json'));
		Assert::same(['asset.js' => 'mapped.asset.js'], $manifestLoader->loadManifest('manifest.json'));

		Assert::throws(function () use ($manifestLoader): void {
			$manifestLoader->loadManifest('unknown.js');
		}, CannotLoadManifestException::class);
	}
}

(new ManifestLoaderTest())->run();
