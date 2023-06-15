<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__ . '/../')
    ->exclude('var');

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        '@PER' => true,
        '@PER:risky' => true,
        '@PHP80Migration:risky' => true,
        '@PHP81Migration' => true,
        '@PHPUnit100Migration:risky' => true,
        'ordered_traits' => false,
        'no_unused_imports' => true,
        'php_unit_test_class_requires_covers' => false,
    ])
    ->setFinder($finder)
    ->setCacheFile(__DIR__ . '/../var/php-cs-fixer.cache');
