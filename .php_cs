<?php

$finder = Symfony\Component\Finder\Finder::create()
    ->notPath('bootstrap/cache')
    ->exclude('storage')
    ->exclude('vendor')
    ->exclude('bootstrap')
    ->in(__DIR__)
    ->name('*.php')
    ->name('_ide_helper')
    ->notName('*.blade.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return PhpCsFixer\Config::create()->setRules([
    '@PSR2'                               => true,
    'array_syntax'                        => ['syntax' => 'short'],
    'ordered_imports'                     => ['sortAlgorithm' => 'alpha'],
    'no_unused_imports'                   => true,
    'array_indentation'                   => true,
    'blank_line_before_return'            => true,
    'method_chaining_indentation'         => true,
    'no_useless_return'                   => true,
    'no_useless_else'                     => true,
    'phpdoc_order'                        => true,
    'phpdoc_separation'                   => true,
    'no_closing_tag'                      => true,
    'no_empty_phpdoc'                     => true,
    'phpdoc_add_missing_param_annotation' => true,
    'phpdoc_align'                        => ['align' => 'vertical'],
    'blank_line_after_opening_tag'        => true,
    'ternary_operator_spaces'             => true,
    'binary_operator_spaces'              => [
        'align_equals'       => true,
        'align_double_arrow' => true,
    ],
])->setFinder($finder);
