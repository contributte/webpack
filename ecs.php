<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Basic\BracesFixer;
use PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocLineSpanFixer;
use SlevomatCodingStandard\Sniffs\Variables\UnusedVariableSniff;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ContainerConfigurator $containerConfigurator): void {
	$services = $containerConfigurator->services();
	$parameters = $containerConfigurator->parameters();

	$parameters->set(Option::FILE_EXTENSIONS, ['php', 'phpt']);
	$parameters->set(Option::EXCLUDE_PATHS, ['temp/*']);

	$parameters->set(Option::INDENTATION, Option::INDENTATION_TAB);
	$parameters->set(Option::SETS, [SetList::CLEAN_CODE, SetList::PSR_12, SetList::PHP_71]);
	$services->set(ClassAttributesSeparationFixer::class);
	$services->set(PhpdocLineSpanFixer::class)
		->call('configure', [[
			'property' => 'single'
		]]);

	$parameters->set(Option::SKIP, [
		UnusedVariableSniff::class => [
			__DIR__ . '/src/Debugging/WebpackPanel.php',
		],
		BracesFixer::class => [
			__DIR__ . '/src/Debugging/WebpackPanel.php',
		],
	]);
};
