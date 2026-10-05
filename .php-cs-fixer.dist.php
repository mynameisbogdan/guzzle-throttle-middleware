<?php

declare(strict_types=1);

use MNIB\CsFixer\MNIBStandardRiskySet;
use MNIB\CsFixer\MNIBStandardSet;
use PhpCsFixer\Config;
use PhpCsFixer\Finder;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

$finder = Finder::create()
    ->in(__DIR__ . '/src')
    ->append([__FILE__])
    ->ignoreDotFiles(true)
    ->ignoreVCS(true)
    ->files()
    ->name('*.php')
;

return (new Config())
    ->setFinder($finder)
    ->registerCustomRuleSets([
        new MNIBStandardSet(),
        new MNIBStandardRiskySet(),
    ])
    ->setParallelConfig(ParallelConfigFactory::detect())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PHP7x4Migration' => true,
        '@MNIB/Standard' => true,
        '@MNIB/Standard:risky' => true,
    ])
;
