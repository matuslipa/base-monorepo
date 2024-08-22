<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $containerConfigurator): void {
    $containerConfigurator->parallel(120, 8);

    // rules sets
    $containerConfigurator->import(SetList::CODE_QUALITY);
    $containerConfigurator->import(SetList::TYPE_DECLARATION);
    $containerConfigurator->import(SetList::DEAD_CODE);
    $containerConfigurator->import(SetList::CODING_STYLE);
    $containerConfigurator->import(SetList::PHP_83);

    // exclude rules from chosen sets
    $containerConfigurator->skip([
        __DIR__ . '/vendor/*',
        __DIR__ . '/bootstrap/cache/*',
        __DIR__ . '/database/*',
        __DIR__ . '/storage/*',
        __DIR__ . '/config/*',
        __DIR__ . '/routes/*',
        __DIR__ . '/resources/*',
        __DIR__ . '/public/*',
        __DIR__ . '/.circleci',
        __DIR__ . '/.github',
        __DIR__ . '/rector.php',
        __DIR__ . '/ecs.php',
        __DIR__ . '/server.php',
        __DIR__ . '/App/Containers/Web/Services/WebFallbackHandlers/WebFallbackHandler.php',
        '*.blade.php',

        // because of calling factory() method
        \Rector\Php71\Rector\FuncCall\RemoveExtraParametersRector::class => [
            __DIR__ . '/App/*/Tests/*',
        ],

        \Rector\CodeQuality\Rector\If_\ExplicitBoolCompareRector::class,
        \Rector\CodeQuality\Rector\FuncCall\CompactToVariablesRector::class,
        \Rector\CodeQuality\Rector\FuncCall\SimplifyRegexPatternRector::class,
        \Rector\CodeQuality\Rector\Concat\JoinStringConcatRector::class,
        \Rector\CodeQuality\Rector\Class_\CompleteDynamicPropertiesRector::class,
        \Rector\CodingStyle\Rector\Catch_\CatchExceptionNameMatchingTypeRector::class,
        \Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector::class,
        \Rector\CodingStyle\Rector\If_\NullableCompareToNullRector::class,
        \Rector\CodingStyle\Rector\String_\SymplifyQuoteEscapeRector::class,
        \Rector\CodeQuality\Rector\Isset_\IssetOnPropertyObjectToPropertyExistsRector::class,
        \Rector\CodingStyle\Rector\Encapsed\WrapEncapsedVariableInCurlyBracesRector::class,
        \Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPrivateMethodRector::class,
        \Rector\Php55\Rector\String_\StringClassNameToClassConstantRector::class,
        \Rector\DeadCode\Rector\ClassMethod\RemoveUselessReturnTagRector::class,
        \Rector\DeadCode\Rector\ClassMethod\RemoveUselessParamTagRector::class,
        \Rector\CodeQuality\Rector\Identical\FlipTypeControlToUseExclusiveTypeRector::class,
        \Rector\TypeDeclaration\Rector\ClassMethod\ParamTypeByMethodCallTypeRector::class,
        \Rector\DeadCode\Rector\Property\RemoveUselessVarTagRector::class,
        \Rector\Php83\Rector\ClassMethod\AddOverrideAttributeToOverriddenMethodsRector::class,
    ]);

    $containerConfigurator->phpVersion(\Rector\ValueObject\PhpVersion::PHP_83);
    $containerConfigurator->parallel();
};
