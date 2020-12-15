<?php

$finder = PhpCsFixer\Finder::create()
    ->in([__DIR__])
    ->files()
    ->name('*.php');

// https://github.com/mlocati/php-cs-fixer-configurator
return PhpCsFixer\Config::create()
    ->setRules(
        [
            // generic PSRs
            '@PSR1' => true,
            '@PSR2' => true,
            'psr0' => true,
            'psr4' => true,

            // imports
            'ordered_imports' => true,
            'no_unused_imports' => true,
            'no_extra_consecutive_blank_lines' => ['use'],
            'php_unit_namespaced' => ['target' => '6.0'],
            'php_unit_expectation' => true,

            // Custom
            'phpdoc_inline_tag' => true,
        ]
    )
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setCacheFile(__DIR__.DIRECTORY_SEPARATOR.'.php_cs.cache');
