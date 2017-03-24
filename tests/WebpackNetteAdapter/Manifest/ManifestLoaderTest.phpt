<?php

declare(strict_types = 1);

namespace OopsTests\WebpackNetteAdapter\Manifest;

use Oops\WebpackNetteAdapter\BuildDirectoryProvider;
use Oops\WebpackNetteAdapter\Manifest\CannotLoadManifestException;
use Oops\WebpackNetteAdapter\Manifest\ManifestLoader;
use Tester\Assert;
use Tester\TestCase;


require_once __DIR__ . '/../../bootstrap.php';


/**
 * @testCase
 */
class ManifestLoaderTest extends TestCase
{

	public function testLoader()
	{
		$buildDirProvider = \Mockery::mock(BuildDirectoryProvider::class);
		$buildDirProvider->shouldReceive('getBuildDirectory')->andReturn(__DIR__);
		$manifestLoader = new ManifestLoader($buildDirProvider);

		Assert::same(__DIR__ . '/manifest.json', $manifestLoader->getManifestPath('manifest.json'));
		Assert::same(['asset.js' => 'resolved.asset.js'], $manifestLoader->loadManifest('manifest.json'));

		Assert::throws(function () use ($manifestLoader) {
			$manifestLoader->loadManifest('unknown.js');
		}, CannotLoadManifestException::class);

		\Mockery::close();
	}

}


(new ManifestLoaderTest())->run();
