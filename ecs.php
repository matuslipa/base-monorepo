<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withRules([
        \PHP_CodeSniffer\Standards\Generic\Sniffs\Formatting\SpaceAfterNotSniff::class,
        \PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer::class,
        \PhpCsFixer\Fixer\Phpdoc\PhpdocNoUselessInheritdocFixer::class,
        \PhpCsFixer\Fixer\Phpdoc\PhpdocTrimFixer::class,
    ])
    ->withConfiguredRule(
        \PhpCsFixer\Fixer\Import\FullyQualifiedStrictTypesFixer::class,
        [
            'phpdoc_tags' => [],
            'leading_backslash_in_global_namespace' => true,
        ],
    )
    ->withConfiguredRule(
        \PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\ForbiddenFunctionsSniff::class,
        [
            'forbiddenFunctions' => [
                'dd' => null,
                'var_dump' => null,
                'xdebug_break' => null,
            ],
        ],
    )
    ->withConfiguredRule(
        \PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer::class,
        [
            'elements' => [
                'property' => 'one',
                'method' => 'one',
            ],
        ],
    )
    ->withConfiguredRule(
        \PhpCsFixer\Fixer\ClassNotation\ClassDefinitionFixer::class,
        [
            'space_before_parenthesis' => true,
            'multi_line_extends_each_single_line' => true,
        ],
    )
    ->withConfiguredRule(
        \PhpCsFixer\Fixer\Operator\ConcatSpaceFixer::class,
        [
            'spacing' => 'one',
        ],
    )
    ->withConfiguredRule(
        \PhpCsFixer\Fixer\CastNotation\CastSpacesFixer::class,
        [
            'space' => 'single',
        ],
    )
    ->withConfiguredRule(
        \PhpCsFixer\Fixer\FunctionNotation\NativeFunctionInvocationFixer::class,
        [
            'include' => ['@internal'],
            'scope' => 'namespaced',
            'strict' => true,
        ],
    )
    ->withConfiguredRule(
        \PhpCsFixer\Fixer\Phpdoc\PhpdocOrderFixer::class,
        [
            'order' => [
                'inheritDoc',
                'test',
                'dataProvider',
                'template',
                'comment',
                'param',
                'return',
                'uses',
                'throws',
            ],
        ],
    )
    ->withConfiguredRule(
        \PhpCsFixer\Fixer\Phpdoc\PhpdocSeparationFixer::class,
        [
            'groups' => [
                ['todo'],
                ['comment'],
                ['param'],
                ['return'],
                ['uses'],
                ['throws'],
            ],
        ],
    )
    ->withConfiguredRule(
        \PhpCsFixer\Fixer\Phpdoc\PhpdocTypesOrderFixer::class,
        [
            'null_adjustment' => 'always_first',
            'sort_algorithm' => 'alpha',
        ],
    )
    ->withSets([
        \Symplify\EasyCodingStandard\ValueObject\Set\SetList::COMMON,
        \Symplify\EasyCodingStandard\ValueObject\Set\SetList::CLEAN_CODE,
        \Symplify\EasyCodingStandard\ValueObject\Set\SetList::PSR_12,
        \Symplify\EasyCodingStandard\ValueObject\Set\SetList::NAMESPACES,
        \Symplify\EasyCodingStandard\ValueObject\Set\SetList::STRICT,
        \Symplify\EasyCodingStandard\ValueObject\Set\SetList::CONTROL_STRUCTURES,
    ])
    ->withPaths([
        __DIR__ . '/App',
        __DIR__ . '/config',
        __DIR__ . '/database',
        __DIR__ . '/routes',
        __DIR__ . '/resources/lang',
    ])
    ->withSkip([
        __DIR__ . '/vendor/',
        __DIR__ . '/themes/',
        __DIR__ . '/storage/',
        __DIR__ . '/bootstrap/cache/',
        __DIR__ . '/.circleci',
        __DIR__ . '/.github',
        '*.blade.php',
        \PhpCsFixer\Fixer\Phpdoc\NoSuperfluousPhpdocTagsFixer::class => null,
        \PHP_CodeSniffer\Standards\Squiz\Sniffs\Classes\ValidClassNameSniff::class . '.NotCamelCaps' => [
            __DIR__ . '/App/Core/Services/CompanyInfoFetcher/Fetchers/*.php',
        ],
        \Symplify\CodingStandard\Fixer\Commenting\RemoveUselessDefaultCommentFixer::class,
    ])
    ->withParallel();
