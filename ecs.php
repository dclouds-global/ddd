<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ClassNotation\OrderedTraitsFixer;
use PhpCsFixer\Fixer\ClassNotation\SingleTraitInsertPerStatementFixer;
use PhpCsFixer\Fixer\ControlStructure\TrailingCommaInMultilineFixer;
use PhpCsFixer\Fixer\ControlStructure\YodaStyleFixer;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PhpCsFixer\Fixer\StringNotation\SingleQuoteFixer;
use PhpCsFixer\Fixer\Whitespace\NoExtraBlankLinesFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withRules([
        OrderedTraitsFixer::class,
        SingleQuoteFixer::class,
        NoExtraBlankLinesFixer::class,
        SingleTraitInsertPerStatementFixer::class,
        DeclareStrictTypesFixer::class,
    ])
    ->withConfiguredRule(
        checkerClass: YodaStyleFixer::class,
        configuration: [
            'equal' => false,
            'identical' => false,
            'less_and_greater' => false,
        ]
    )
    ->withConfiguredRule(
        checkerClass: TrailingCommaInMultilineFixer::class,
        configuration: [
            'elements' => ['arrays'],
        ]
    )
    ->withPreparedSets(
        cleanCode: true
    )
    ->withParallel();
