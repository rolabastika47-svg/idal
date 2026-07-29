<?php

namespace Breakdance\CLI;

use WP_CLI;
use WP_CLI_Command;

/**
 * Commands for extracting translatable strings from Breakdance elements.
 */
class ElementI18nCommand extends WP_CLI_Command {

    /**
     * Generate a POT file from Breakdance element strings.
     *
     * Scans all element.php files in the specified plugin and extracts:
     * - Element names from name() method
     * - Control labels from c() function calls
     * - Dropdown 'text' values
     * - Button bar 'label' values
     * - Preset section labels from getPresetSection() calls
     *
     * ## OPTIONS
     *
     * <plugin-slug>
     * : The plugin slug to scan for element files.
     *
     * [--domain=<domain>]
     * : Text domain to use. Defaults to plugin slug.
     *
     * [--output=<path>]
     * : Output path for POT file. Defaults to {plugin}/languages/{domain}.pot
     *
     * [--skip-audit]
     * : Skip the detailed audit report.
     *
     * ## EXAMPLES
     *
     *     # Generate POT for breakdance-elements
     *     wp breakdance i18n make_pot breakdance-elements
     *
     *     # Use custom domain
     *     wp breakdance i18n make_pot my-plugin --domain=my-domain
     *
     *     # Custom output path
     *     wp breakdance i18n make_pot my-plugin --output=/path/to/custom.pot
     *
     * @when after_wp_load
     */
    public function make_pot( $args, $assoc_args ) {
        $plugin_slug = $args[0];
        $domain = $assoc_args['domain'] ?? $plugin_slug;
        $skip_audit = isset( $assoc_args['skip-audit'] );

        // Determine plugin path
        $plugin_path = WP_PLUGIN_DIR . '/' . $plugin_slug;

        if ( ! is_dir( $plugin_path ) ) {
            WP_CLI::error( "Plugin directory not found: {$plugin_path}" );
        }

        WP_CLI::log( "Scanning plugin: {$plugin_path}" );
        WP_CLI::log( "Text domain: {$domain}" );
        WP_CLI::log( "" );

        // Find all element.php files
        $element_files = $this->find_element_files( $plugin_path );

        if ( empty( $element_files ) ) {
            WP_CLI::warning( "No element.php files found in {$plugin_path}" );
            return;
        }

        // Extract strings from all files
        $all_strings = [];
        $stats = [
            'files' => 0,
            'element_names' => 0,
            'control_labels' => 0,
            'item_text' => 0,
            'item_label' => 0,
            'preset_section_labels' => 0,
            'total' => 0,
        ];

        foreach ( $element_files as $file ) {
            $relative_path = str_replace( WP_PLUGIN_DIR . '/', '', $file );
            WP_CLI::log( "Processing: {$relative_path}" );

            $extracted = $this->extract_strings_from_file( $file );

            foreach ( $extracted as $string ) {
                $all_strings[] = $string;
                $stats['total']++;
                $stats[ $string['type'] ]++;
            }

            $stats['files']++;
        }

        // Remove duplicates while preserving context
        $unique_strings = $this->deduplicate_strings( $all_strings );

        // Generate output path
        if ( isset( $assoc_args['output'] ) ) {
            $output_path = $assoc_args['output'];
        } else {
            $languages_dir = $plugin_path . '/languages';
            if ( ! is_dir( $languages_dir ) ) {
                wp_mkdir_p( $languages_dir );
            }
            $output_path = $languages_dir . '/' . $domain . '-builder.pot';
        }

        // Generate POT file
        $this->generate_pot_file( $unique_strings, $output_path, $domain, $plugin_slug, $plugin_slug );

        // Show audit report
        if ( ! $skip_audit ) {
            $this->show_audit_report( $stats, count( $unique_strings ) );
        }

        WP_CLI::success( sprintf(
            'Generated %s with %d unique strings from %d files.',
            $output_path,
            count( $unique_strings ),
            $stats['files']
        ) );
    }

    /**
     * Find all element.php files in a directory
     */
    private function find_element_files( $path ) {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator( $path, \RecursiveDirectoryIterator::SKIP_DOTS )
        );

        $element_files = [];
        foreach ( $iterator as $file ) {
            if ( $file->isFile() && $file->getFilename() === 'element.php' ) {
                $element_files[] = $file->getPathname();
            }
        }

        return $element_files;
    }

    /**
     * Extract translatable strings from an element file
     */
    private function extract_strings_from_file( $file ) {
        $content = file_get_contents( $file );
        $strings = [];
        $relative_path = str_replace( WP_PLUGIN_DIR . '/', '', $file );

        // Skip experimental elements
        if ( preg_match( '/static\s+function\s+experimental\s*\(\s*\)\s*\{([^}]*)\}/', $content, $experimental_match ) ) {
            // Check if the method body contains 'return true'
            if ( preg_match( '/return\s+true\s*;/', $experimental_match[1] ) ) {
                return [];
            }
        }

        // Extract element name from: static function name() { return 'Name'; }
        if ( preg_match( '/static\s+function\s+name\s*\(\s*\)\s*\{[^}]*return\s+[\'"]([^\'"]+)[\'"]/', $content, $match, PREG_OFFSET_CAPTURE ) ) {
            $strings[] = [
                'string' => $match[1][0],
                'type' => 'element_names',
                'context' => 'Element name',
                'file' => $relative_path,
                'line' => $this->get_line_number( $content, $match[1][1] ),
            ];
        }

        // Matches: c( 'slug', "Label" or c("slug", 'Label'
        preg_match_all( '/\bc\s*\(\s*[\'"][^\'"]*[\'"]\s*,\s*[\'"]([^\'"]+)[\'"]/', $content, $matches, PREG_OFFSET_CAPTURE );
        foreach ( $matches[1] as $match ) {
            if ( $this->is_translatable_string( $match[0] ) ) {
                $strings[] = [
                    'string' => $match[0],
                    'type' => 'control_labels',
                    'context' => 'Control label',
                    'file' => $relative_path,
                    'line' => $this->get_line_number( $content, $match[1] ),
                ];
            }
        }

        // Extract 'text' values from items arrays (only unwrapped strings): 'text' => 'Value'
        // Skip if already wrapped in __()
        preg_match_all( '/[\'"]text[\'"]\s*=>\s*[\'"]([^\'"]+)[\'"]/', $content, $matches, PREG_OFFSET_CAPTURE );
        foreach ( $matches[1] as $match ) {
            // Check if this text value is wrapped in a translation function
            $position = $match[1];
            $before_context = substr( $content, max( 0, $position - 50 ), 50 );

            // Skip if preceded by __(
            if ( preg_match( '/__\s*\(\s*[\'"]?$/', $before_context ) ) {
                continue;
            }

            if ( $this->is_translatable_string( $match[0] ) ) {
                $strings[] = [
                    'string' => $match[0],
                    'type' => 'item_text',
                    'context' => 'Item text',
                    'file' => $relative_path,
                    'line' => $this->get_line_number( $content, $position ),
                ];
            }
        }

        // Extract 'label' values from arrays (only unwrapped strings): 'label' => 'Value'
        // Skip if already wrapped in __()
        preg_match_all( '/[\'"]label[\'"]\s*=>\s*[\'"]([^\'"]+)[\'"]/', $content, $matches, PREG_OFFSET_CAPTURE );
        foreach ( $matches[1] as $match ) {
            // Check if this label value is wrapped in a translation function
            $position = $match[1];
            $before_context = substr( $content, max( 0, $position - 50 ), 50 );

            // Skip if preceded by __(
            if ( preg_match( '/__\s*\(\s*[\'"]?$/', $before_context ) ) {
                continue;
            }

            if ( $this->is_translatable_string( $match[0] ) && $match[0] !== 'Label' ) {
                $strings[] = [
                    'string' => $match[0],
                    'type' => 'item_label',
                    'context' => 'Item label',
                    'file' => $relative_path,
                    'line' => $this->get_line_number( $content, $position ),
                ];
            }
        }

        // Extract labels from getPresetSection calls
        // Pattern: getPresetSection( "First", "Label", "Third", ... )
        preg_match_all( '/getPresetSection\s*\(\s*[\'"][^\'"]*[\'"]\s*,\s*[\'"]([^\'"]+)[\'"]/', $content, $matches, PREG_OFFSET_CAPTURE );
        foreach ( $matches[1] as $match ) {
            if ( $this->is_translatable_string( $match[0] ) ) {
                $strings[] = [
                    'string' => $match[0],
                    'type' => 'preset_section_labels',
                    'context' => 'Preset section label',
                    'file' => $relative_path,
                    'line' => $this->get_line_number( $content, $match[1] ),
                ];
            }
        }

        return $strings;
    }

    /**
     * Get line number from byte offset
     */
    private function get_line_number( $content, $offset ) {
        $substring = substr( $content, 0, $offset );
        return substr_count( $substring, "\n" ) + 1;
    }

    /**
     * Check if a string should be translated
     */
    private function is_translatable_string( $string ) {
        // Skip empty strings
        if ( empty( trim( $string ) ) ) {
            return false;
        }

        // Skip 'null' literal
        if ( $string === 'null' ) {
            return false;
        }

        // Skip very short strings that are likely technical (e.g., 'px', 'em')
        if ( strlen( $string ) < 2 ) {
            return false;
        }

        // Skip strings that are just numbers
        if ( is_numeric( $string ) ) {
            return false;
        }

        return true;
    }

    /**
     * Merge duplicate strings by grouping files together
     * Groups by string + context since _x() considers both
     */
    private function deduplicate_strings( $strings ) {
        $grouped = [];

        foreach ( $strings as $item ) {
            // Create key from both string and context since _x() differentiates by context
            $key = $item['string'] . '|' . $item['context'];

            if ( ! isset( $grouped[ $key ] ) ) {
                $grouped[ $key ] = [
                    'string' => $item['string'],
                    'type' => $item['type'],
                    'context' => $item['context'],
                    'locations' => [ [
                        'file' => $item['file'],
                        'line' => $item['line'] ?? null,
                    ] ],
                ];
            } else {
                // Add file:line combination if not already present
                $location = [
                    'file' => $item['file'],
                    'line' => $item['line'] ?? null,
                ];

                $exists = false;
                foreach ( $grouped[ $key ]['locations'] as $existing ) {
                    if ( $existing['file'] === $location['file'] && $existing['line'] === $location['line'] ) {
                        $exists = true;
                        break;
                    }
                }

                if ( ! $exists ) {
                    $grouped[ $key ]['locations'][] = $location;
                }
            }
        }

        return array_values( $grouped );
    }

    /**
     * Generate POT file
     */
    private function generate_pot_file( $strings, $output_path, $domain, $package_name, $plugin_slug ) {
        $pot_header = $this->get_pot_header( $domain, $package_name );

        $pot_entries = [];
        foreach ( $strings as $item ) {
            // Handle both old format (files array) and new format (locations array)
            $locations = isset( $item['locations'] ) ? $item['locations'] :
                ( isset( $item['files'] ) ? array_map( function( $file ) {
                    return [ 'file' => $file, 'line' => null ];
                }, $item['files'] ) : [ [ 'file' => $item['file'], 'line' => $item['line'] ?? null ] ] );

            // Create file references with line numbers
            $file_refs = array_map( function( $location ) use ( $plugin_slug ) {
                // Remove plugin slug prefix from file path
                $cleaned_file = preg_replace( '#^' . preg_quote( $plugin_slug, '#' ) . '/#', '', $location['file'] );

                // Add line number if available
                if ( isset( $location['line'] ) && $location['line'] !== null ) {
                    return "#: {$cleaned_file}:{$location['line']}";
                }
                return "#: {$cleaned_file}";
            }, $locations );

            $pot_entries[] = sprintf(
                "%s\nmsgctxt \"%s\"\nmsgid \"%s\"\nmsgstr \"\"\n",
                implode( "\n", $file_refs ),
                $item['context'],
                addslashes( $item['string'] )
            );
        }

        $pot_content = $pot_header . "\n" . implode( "\n", $pot_entries );

        file_put_contents( $output_path, $pot_content );
    }

    /**
     * Get POT file header
     */
    private function get_pot_header( $domain, $package_name ) {
        $date = date( 'Y-m-d H:iO' );

        return <<<POT
# Translation file for {$package_name}
# Copyright (C) {$date} {$package_name}
# This file is distributed under the same license as the {$package_name} package.
msgid ""
msgstr ""
"Project-Id-Version: {$package_name}\\n"
"Report-Msgid-Bugs-To: https://breakdance.com/support\\n"
"POT-Creation-Date: {$date}\\n"
"PO-Revision-Date: YEAR-MO-DA HO:MI+ZONE\\n"
"Last-Translator: Breakdance <support@breakdance.com>\\n"
"Language-Team: Breakdance <support@breakdance.com>\\n"
"Language: en\\n"
"MIME-Version: 1.0\\n"
"Content-Type: text/plain; charset=UTF-8\\n"
"Content-Transfer-Encoding: 8bit\\n"
"X-Generator: Breakdance Element i18n CLI\\n"
"X-Domain: {$domain}\\n"
POT;
    }

    /**
     * Show audit report
     */
    private function show_audit_report( $stats, $unique_count ) {
        WP_CLI::log( "" );
        WP_CLI::log( "Extraction Report:" );
        WP_CLI::log( str_repeat( '=', 60 ) );
        WP_CLI::log( sprintf( "Files scanned: %d", $stats['files'] ) );
        WP_CLI::log( sprintf( "Total strings found: %s", number_format( $stats['total'] ) ) );
        WP_CLI::log( sprintf( "Unique strings: %d", $unique_count ) );
        WP_CLI::log( "" );
        WP_CLI::log( "By type:" );
        WP_CLI::log( sprintf( "  - Element name: %d", $stats['element_names'] ) );
        WP_CLI::log( sprintf( "  - Control label: %d", $stats['control_labels'] ) );
        WP_CLI::log( sprintf( "  - Item text: %d", $stats['item_text'] ) );
        WP_CLI::log( sprintf( "  - Item label: %d", $stats['item_label'] ) );
        WP_CLI::log( sprintf( "  - Preset section label: %d", $stats['preset_section_labels'] ) );
        WP_CLI::log( str_repeat( '=', 60 ) );
        WP_CLI::log( "" );
    }
}

\WP_CLI::add_command( 'breakdance i18n', 'Breakdance\CLI\ElementI18nCommand' );
