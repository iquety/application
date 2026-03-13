<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

return (new Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@auto' => true,
        '@auto:risky' => true,
        '@PSR1' => true,
        '@PSR12' => true,
        '@PER-CS' => true,
        'trailing_comma_in_multiline' => false,
        'new_expression_parentheses' => false,
        'static_lambda' => false,
        'new_with_parentheses' => true,
        'no_unused_imports' => true,
        'no_homoglyph_names' => true,
        'declare_strict_types' => true,
        'single_quote' => true,
        'no_spaces_around_offset' => true,
        'no_empty_comment' => true,
        'single_line_comment_spacing' => true,
        'single_line_comment_style' => true,
        'explicit_indirect_variable' => true,
        'method_chaining_indentation' => true,
        'operator_linebreak' => true,
        'whitespace_after_comma_in_array' => true,
        'return_assignment' => true,
        'phpdoc_types_order' => true,
        'phpdoc_types_no_duplicates' => true,
        'ordered_types' => true,
        'ordered_class_elements' => true
    ])
    // 💡 by default, Fixer looks for `*.php` files excluding `./vendor/` - here, you can groom this config
    ->setFinder(
        (new Finder())
            // 💡 root folder to check
            ->in(__DIR__)
            // 💡 additional files, eg bin entry file
            // ->append([__DIR__.'/bin-entry-file'])
            // 💡 folders to exclude, if any
            // ->exclude([/* ... */])
            // 💡 path patterns to exclude, if any
            // ->notPath([/* ... */])
            // 💡 extra configs
            // ->ignoreDotFiles(false) // true by default in v3, false in v4 or future mode
            // ->ignoreVCS(true) // true by default
    )
;
