<?php

$finder = (new PhpCsFixer\Finder())
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/features'
    ])
    ->exclude('var')
;

return (new PhpCsFixer\Config())
    ->setLineEnding("\r\n")
    ->setRiskyAllowed(true)
    ->setRules([
        'array_syntax' => ['syntax' => 'short'],
        'declare_strict_types' => true,
        'no_unused_imports' => true,
        'ordered_imports' => true,
    ])
    ->setFinder($finder)
    ->setCacheFile('.php-cs-fixer.cache')
;
