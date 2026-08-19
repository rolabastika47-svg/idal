<?php

// @psalm-ignore-file

/**
 * Vite manifest entry structure
 * @typedef array{file: string, src?: string, isEntry?: bool, imports?: string[], css?: string[]} ManifestChunk
 */

function getProductionManifest()
{
    $distPath = breakdanceBuilderDistPath();
    $manifestPath = "{$distPath}/.vite/manifest.json";
    $manifest = json_decode(file_get_contents($manifestPath), true);

    // Transform the manifest URLs to use the correct base path
    $basePath = breakdanceBuilderDistUrl();

    foreach ($manifest as $name => &$chunk) {
        if (isset($chunk['file'])) {
            $chunk['file'] = $basePath . '/' . $chunk['file'];
        }
        if (isset($chunk['css'])) {
            $chunk['css'] = array_map(fn($css) => $basePath . '/' . $css, $chunk['css']);
        }
    }

    return $manifest;
}

/**
 * Monorepo root (parent of wp-plugins/breakdance-main).
 */
function breakdanceMonorepoRootPath(): string
{
    return dirname(__BREAKDANCE_DIR__, 2);
}

function shouldUseMonorepoPath(): bool
{
    return defined('BREAKDANCE_USE_MONOREPO_PATH') && BREAKDANCE_USE_MONOREPO_PATH;
}

/**
 * When dev:codespace runs (vite build --watch), a .build-watch-mode marker
 * file is created in the builder app root.  The loader should use the
 * production manifest instead of the Vite dev server in that case.
 */
function isViteBuildWatchMode(): bool
{
    if (shouldUseMonorepoPath()) {
        return file_exists(breakdanceMonorepoRootPath() . '/apps/builder/.build-watch-mode');
    }

    return false;
}

function shouldUseViteDevServer(): bool
{
    return \Breakdance\Admin\get_env() === 'local' && !isViteBuildWatchMode();
}

function breakdanceBuilderDistPath(): string
{
    if (shouldUseMonorepoPath()) {
        return breakdanceMonorepoRootPath() . '/apps/builder/dist';
    }

    return __BREAKDANCE_DIR__ . '/builder/dist';
}

function breakdanceBuilderDistUrl(): string
{
    if (shouldUseMonorepoPath()) {
        return plugins_url('apps/builder/dist', breakdanceMonorepoRootPath() . '/package.json');
    }

    return plugin_dir_url(__BREAKDANCE_PLUGIN_FILE__) . 'builder/dist';
}

/**
 * Get all imported chunks recursively from a Vite manifest entry
 *
 * @param array $manifest The full Vite manifest
 * @param string $entryName The entry point name (e.g., "src/main.ts")
 * @return array{chunks: array, css: string[]} Array of imported chunks and collected CSS files
 */
function getImportedChunks(array $manifest, string $entryName): array
{
    $seen = [];
    $cssFiles = [];

    $getChunksRecursive = function (array $chunk) use ($manifest, &$seen, &$cssFiles, &$getChunksRecursive): array {
        $chunks = [];

        // Collect CSS files from this chunk
        if (isset($chunk['css'])) {
            foreach ($chunk['css'] as $css) {
                if (!in_array($css, $cssFiles, true)) {
                    $cssFiles[] = $css;
                }
            }
        }

        // Process imports recursively
        if (isset($chunk['imports'])) {
            foreach ($chunk['imports'] as $importKey) {
                if (isset($seen[$importKey])) {
                    continue;
                }
                $seen[$importKey] = true;

                if (isset($manifest[$importKey])) {
                    $importee = $manifest[$importKey];
                    $chunks = array_merge($chunks, $getChunksRecursive($importee));
                    $chunks[] = $importee;
                }
            }
        }

        return $chunks;
    };

    $entryChunk = $manifest[$entryName] ?? null;
    $chunks = $entryChunk ? $getChunksRecursive($entryChunk) : [];

    return [
        'chunks' => $chunks,
        'css' => $cssFiles,
        'entry' => $entryChunk,
    ];
}

/**
 * Map app names to their Vite entry point paths
 */
function getViteEntryPath(string $appName): string
{
    $entryMap = [
        'app' => 'src/main.ts',
        'manage-templates' => 'src/subapps/manage-templates/entry.ts',
        'design-library' => 'src/subapps/design-library/entry.ts',
        'settings-tools-regenerate-cache' => 'src/subapps/regenerate-cache/entry.ts',
        'onboarding-app' => 'src/subapps/onboarding/entry.ts',
    ];

    return $entryMap[$appName] ?? "src/{$appName}/main.ts";
}

/**
 * Port for the builder Vite dev server.
 * Set `BREAKDANCE_VITE_DEV_PORT` in wp-config.
 */
function breakdanceViteDevServerPort(): int
{
    if (defined('BREAKDANCE_VITE_DEV_PORT')) {
        return (int) BREAKDANCE_VITE_DEV_PORT;
    }

    return 8080;
}

function getViteDevServerOrigin(): string
{
    $port = breakdanceViteDevServerPort();

    if (defined('BREAKDANCE_VITE_DEV_SERVER')) {
        return BREAKDANCE_VITE_DEV_SERVER;
    }

    return '//localhost:' . $port;
}

/**
 * @param string $path Path under the Vite root (e.g. src/main.ts)
 */
function getViteDevServerUrl(string $path): string
{
    return \getViteDevServerOrigin() . '/' . \ltrim($path, '/');
}

/**
 * Inline <style> that declares the Vuetify CSS cascade layer order.
 * Must be output before any stylesheet that uses @layer vuetify.*.
 */
function getCssLayerOrderStyle(): string
{
    return '<style>
      @layer vuetify {
        @layer reset,transitions,base,components,overrides,theme.background,theme.foreground,utilities;
      }
    </style>';
}

function getProductionHeadLinks($manifest, $appName)
{
    ob_start();

    loadI18nScripts($appName);

    // Handle favicons — these are in public/ so Vite copies them to the dist root
    // as-is without hashing, meaning they are never in the manifest. Build the URL
    // directly from the dist base path, mirroring what getDevelopmentHeadLinks does
    // for the Vite dev server.
    $basePath = breakdanceBuilderDistUrl();
    $faviconLight = $basePath . '/' . (BREAKDANCE_MODE === 'oxygen' ? 'favicon-oxygen.svg' : 'favicon.svg');
    $faviconDark = $basePath . '/' . (BREAKDANCE_MODE === 'oxygen' ? 'favicon-oxygen-dark.svg' : 'favicon-dark.svg');
?>
        <link rel="icon" class="js-site-favicon" type="image/svg+xml" href="<?php echo esc_url($faviconLight); ?>" media="(prefers-color-scheme: light)">
        <link rel="icon" class="js-site-favicon" type="image/svg+xml" href="<?php echo esc_url($faviconDark); ?>" media="(prefers-color-scheme: dark)">
    <?php

    // Establish CSS cascade layer order before any stylesheets load.
    // Vuetify uses @layer when $layers: true is set. The order declaration must
    // appear before any @layer usage, otherwise browsers use first-seen order
    // which breaks when Rollup code-splits CSS across chunks.
    echo getCssLayerOrderStyle();

    // Get the entry point path for this app
    $entryPath = getViteEntryPath($appName);
    $importData = getImportedChunks($manifest, $entryPath);

    // Preload and load all CSS files from imported chunks
    foreach ($importData['css'] as $cssFile) {
    ?>
        <link href="<?php echo $cssFile; ?>" rel="preload" as="style">
        <link href="<?php echo $cssFile; ?>" rel="stylesheet">
        <?php
    }

    // Preload all JS chunks (imports first, then entry)
    foreach ($importData['chunks'] as $chunk) {
        if (isset($chunk['file']) && str_ends_with($chunk['file'], '.js')) {
        ?>
            <link href="<?php echo $chunk['file']; ?>" rel="modulepreload">
        <?php
        }
    }

    // Preload the entry point JS
    if (isset($importData['entry']['file'])) {
        ?>
        <link href="<?php echo $importData['entry']['file']; ?>" rel="modulepreload">
    <?php
    }

    return ob_get_clean();
}

function getProductionFooterScripts($manifest, $appName)
{
    ob_start();

    // Get the entry point path for this app
    $entryPath = getViteEntryPath($appName);
    $importData = getImportedChunks($manifest, $entryPath);

    // With Vite, we only need to load the entry point as a module
    // The browser will handle loading the imports automatically
    if (isset($importData['entry']['file'])) {
    ?>
        <script type="module" src="<?php echo $importData['entry']['file']; ?>"></script>
    <?php
    }

    return ob_get_clean();
}

function getDevelopmentHeadLinks($appName)
{
    ob_start();

    loadI18nScripts($appName);

    $entryPath = getViteEntryPath($appName);

    echo getCssLayerOrderStyle();
    $faviconLight = BREAKDANCE_MODE === 'oxygen' ? 'favicon-oxygen.svg' : 'favicon.svg';
    $faviconDark = BREAKDANCE_MODE === 'oxygen' ? 'favicon-oxygen-dark.svg' : 'favicon-dark.svg';
    ?>
    <link rel="icon" class="js-site-favicon" type="image/svg+xml" href="<?php echo esc_url(getViteDevServerUrl($faviconLight)); ?>" media="(prefers-color-scheme: light)">
    <link rel="icon" class="js-site-favicon" type="image/svg+xml" href="<?php echo esc_url(getViteDevServerUrl($faviconDark)); ?>" media="(prefers-color-scheme: dark)">
    <link href="<?php echo esc_url(getViteDevServerUrl($entryPath)); ?>" rel="modulepreload">
<?php

    return ob_get_clean();
}

function getDevelopmentFooterScripts($appName)
{
    ob_start();

    $entryPath = getViteEntryPath($appName);
?>
    <script type="module" src="<?php echo esc_url(getViteDevServerUrl('@vite/client')); ?>"></script>
    <script type="module" src="<?php echo esc_url(getViteDevServerUrl($entryPath)); ?>"></script>
<?php
    return ob_get_clean();
}

/**
 * @param string $handle // Must be a handle that WP registers automatically
 * @return string
 */
function getEnqueuedScriptUrl($handle)
{
    wp_enqueue_script($handle);

    $wp_scripts = wp_scripts();
    $script = $wp_scripts->registered[$handle];

    if (!$script) return '';

    return \site_url($script->src . "?ver=" . $script->ver);
}

function loadI18nScripts($appName)
{
    $language = get_user_locale();
    $path = plugin_dir_path(__BREAKDANCE_PLUGIN_FILE__) . 'languages/breakdance-' . $language . '.json';

    if (!file_exists($path)) {
        $language = str_replace("_", "-", $language);
        $path = plugin_dir_path(__BREAKDANCE_PLUGIN_FILE__) . 'languages/breakdance-' . $language . '.json';
    }

    $json = load_script_translations($path, 'builder', 'breakdance') ?: '{}';
    $json = apply_filters('breakdance_i18n_json', $json);

    // wp i18n requires wp-hooks
    $hooksUrl = getEnqueuedScriptUrl('wp-hooks');
    $i18nUrl = getEnqueuedScriptUrl('wp-i18n');

    // Copied from what "wp_set_script_translations" outputs
    $output = <<<JS
      ( function( domain, translations ) {
          if (!translations?.locale_data) return;
          var localeData = translations.locale_data[ domain ] || translations.locale_data.messages;
          localeData[""].domain = domain;
          wp.i18n.setLocaleData( localeData, domain );
      } )( "breakdance", {$json} );
    JS;

    global $wp_locale;
    $textDirection = $wp_locale->text_direction;

?>
    <script type="text/javascript" src="<?= $hooksUrl ?>"></script>
    <script type="text/javascript" src="<?= $i18nUrl ?>"></script>
    <script>
        wp.i18n.setLocaleData({
            'text direction\u0004<?= $textDirection ?>': ['<?= $textDirection ?>']
        });
    </script>

    <script>
        <?php echo $output; ?>
    </script>
<?php
}
