<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude(['vendor', 'runtime', 'web']);

return (new PhpCsFixer\Config())
    ->setRules([
        '@DoctrineAnnotation' => true,
        '@PSR12' => true,
        '@Symfony' => true,
        'no_superfluous_phpdoc_tags' => false,
        'concat_space' => [
            'spacing' => 'one',
        ],
        'global_namespace_import' => false,
        'phpdoc_summary' => false,
        'phpdoc_tag_type' => false,
        'blank_line_before_statement' => [
            'statements' => [
                'break',
                'case',
                'continue',
                'declare',
                'default',
                'exit',
                'goto',
                'include',
                'include_once',
                'require',
                'require_once',
                'return',
                'throw',
                'yield',
                'yield_from',
            ],
        ],
        'single_line_throw' => false,
        'phpdoc_annotation_without_dot' => false,
        'phpdoc_align' => [
            'align' => 'left',
        ],
        'yoda_style' => false,
        'phpdoc_types_order' => false,
    ])
    ->setFinder($finder)
    ->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect());
