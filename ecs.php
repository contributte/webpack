<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocLineSpanFixer;
use PhpCsFixer\Fixer\StringNotation\SingleQuoteFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ECSConfig $ecsConfig): void {
	$ecsConfig->fileExtensions(['php', 'phpt']);
	$ecsConfig->skip(['temp/*']);

	$ecsConfig->sets([SetList::CLEAN_CODE, SetList::PSR_12]);
	$ecsConfig->indentation(Option::INDENTATION_TAB);

	$ecsConfig->rule(SingleQuoteFixer::class);
	$ecsConfig->rule(ClassAttributesSeparationFixer::class);
	$ecsConfig->ruleWithConfiguration(PhpdocLineSpanFixer::class, ['property' => 'single']);
};
