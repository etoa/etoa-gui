<?php declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
  ->files()
  ->name('*.php')
  ->in(__DIR__.'/src')
  ->in(__DIR__.'/tests')
;

$config = new PhpCsFixer\Config();
$config
  ->setRiskyAllowed(true)
  ->setRules([
    '@PSR2' => true,
    '@PHP71Migration:risky' => true,
    'binary_operator_spaces' => true,
    'blank_line_before_statement' => true,
    'function_typehint_space' => true,
    'no_empty_comment' => true,
    'no_empty_phpdoc' => true,
    'no_empty_statement' => true,
    'no_leading_namespace_whitespace' => true,
    'trailing_comma_in_multiline' => true,
    'space_after_semicolon' => true,
    'no_unused_imports' => true,
  ])
  ->setFinder($finder)
;


return $config;
