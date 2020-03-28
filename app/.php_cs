<?php

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/features',
        __DIR__ . '/config',
        __DIR__ . '/public',
    ])
;

return PhpCsFixer\Config::create()
    ->setLineEnding("\r\n")
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
        'declare_strict_types' => true,
        'ordered_imports' => true,
        'ordered_class_elements' => true,
        'psr4' => true,
    ])
    ->setFinder($finder)
;
