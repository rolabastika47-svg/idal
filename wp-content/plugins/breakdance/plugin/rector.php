<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;
use Rector\Php74\Rector\FuncCall\ArrayKeyExistsOnPropertyRector;
use Rector\Set\ValueObject\LevelSetList;

return RectorConfig::configure()
    ->withSkip([
        // Disable the following rules
        ClosureToArrowFunctionRector::class,
        ArrayKeyExistsOnPropertyRector::class,
        // Skip the following paths
         '**/node_modules/*',
        __DIR__ . '/lib',
        __DIR__ . '/dev',
        __DIR__ . '/tests',
        __DIR__ . '/vendor',
    ])
    ->withPaths([
        __DIR__ . '/acf-breakdance-content',
        __DIR__ . '/actions_filters',
        __DIR__ . '/admin',
        __DIR__ . '/ajax',
        __DIR__ . '/animations',
        __DIR__ . '/api-keys',
        __DIR__ . '/bloat-eliminator',
        __DIR__ . '/blocks',
        __DIR__ . '/breakdance-oxygen',
        __DIR__ . '/browse-mode',
        __DIR__ . '/classes-selectors',
        __DIR__ . '/compat',
        __DIR__ . '/conditions',
        __DIR__ . '/config',
        __DIR__ . '/customizer',
        __DIR__ . '/debug',
        __DIR__ . '/defines',
        __DIR__ . '/design-library',
        __DIR__ . '/design-presets',
        __DIR__ . '/element-studio',
        __DIR__ . '/elements',
        __DIR__ . '/error-reporter',
        __DIR__ . '/experiments',
        __DIR__ . '/extensions',
        __DIR__ . '/filesystem',
        __DIR__ . '/fonts',
        __DIR__ . '/forms',
        __DIR__ . '/framework',
        __DIR__ . '/futurelayer',
        __DIR__ . '/global-default-stylesheets',
        __DIR__ . '/global-scripts',
        __DIR__ . '/global-styles',
        __DIR__ . '/gutenberg',
        __DIR__ . '/header-footer-tracking-code',
        __DIR__ . '/icons',
        __DIR__ . '/integrations',
        __DIR__ . '/interactions',
        __DIR__ . '/licensing',
        __DIR__ . '/loader',
        __DIR__ . '/local-dependencies-files',
        __DIR__ . '/maintenance-mode',
        __DIR__ . '/media',
        __DIR__ . '/oembed',
        __DIR__ . '/onboarding',
        __DIR__ . '/partners',
        __DIR__ . '/permissions',
        __DIR__ . '/plugins-api',
        __DIR__ . '/preferences',
        __DIR__ . '/psalm-types',
        __DIR__ . '/public-apis',
        __DIR__ . '/render',
        __DIR__ . '/revisions',
        __DIR__ . '/security',
        __DIR__ . '/sessions',
        __DIR__ . '/settings',
        __DIR__ . '/setup',
        __DIR__ . '/setup-wizard',
        __DIR__ . '/singularity',
        __DIR__ . '/subscription',
        __DIR__ . '/tests',
        __DIR__ . '/themeless',
        __DIR__ . '/tracking',
        __DIR__ . '/util',
        __DIR__ . '/woocommerce',
        __DIR__ . '/wp-query-control',
        __DIR__ . '/wpwidgets',
    ])
    // uncomment to reach your current PHP version
    // ->withPhpSets()
    ->withSets([
        LevelSetList::UP_TO_PHP_84,
    ]);

