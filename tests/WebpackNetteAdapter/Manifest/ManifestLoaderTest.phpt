<?php

declare(strict_types=1);

namespace OopsTests\WebpackNetteAdapter\Manifest;

use Oops\WebpackNetteAdapter\BuildDirectoryProvider;
use Oops\WebpackNetteAdapter\Manifest\CannotLoadManifestException;
use Oops\WebpackNetteAdapter\Manifest\ManifestLoader;
use Oops\WebpackNetteAdapter\Manifest\ManifestMapper;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../bootstrap.php';


/**
 * @testCase
 */
class ManifestLoaderTest extends TestCase
{
	public function testLoader(): void
	{
		$buildDirProvider = \Mockery::mock(BuildDirectoryProvider::class);
		$buildDirProvider->shouldReceive('getBuildDirectory')->andReturn(__DIR__);

		$manifestMapper = \Mockery::mock(ManifestMapper::class);
		$manifestMapper->shouldReceive('map')
			->with(['asset.js' => 'resolved.asset.js'])
			->andReturn(['asset.js' => 'mapped.asset.js']);

		$manifestLoader = new ManifestLoader($buildDirProvider, $manifestMapper);

		Assert::same(__DIR__ . '/manifest.json', $manifestLoader->getManifestPath('manifest.json'));
		Assert::same(['asset.js' => 'mapped.asset.js'], $manifestLoader->loadManifest('manifest.json'));

		Assert::throws(function () use ($manifestLoader): void {
			$manifestLoader->loadManifest('unknown.js');
		}, CannotLoadManifestException::class);

		\Mockery::close();
	}
}


(new ManifestLoaderTest())->run();
