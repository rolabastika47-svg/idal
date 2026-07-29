<?php
/**
 * Helper Functions
 * 
 * Reusable utility functions for the OMSAR theme
 *
 * @package OMSAR
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Format date with Arabic month names based on current language
 * 
 * @param DateTime $date_obj The DateTime object to format
 * @param string $current_lang The current language code ('ar' for Arabic, 'en' for English)
 * @return string Formatted date string with appropriate month names
 */
function format_date_with_arabic_months($date_obj, $current_lang) {
    if ($current_lang === 'ar') {
        // Arabic month names
        $arabic_months = array(
            'Jan' => 'يناير',
            'Feb' => 'فبراير',
            'Mar' => 'مارس',
            'Apr' => 'أبريل',
            'May' => 'مايو',
            'Jun' => 'يونيو',
            'Jul' => 'يوليو',
            'Aug' => 'أغسطس',
            'Sep' => 'سبتمبر',
            'Oct' => 'أكتوبر',
            'Nov' => 'نوفمبر',
            'Dec' => 'ديسمبر'
        );
        
        $formatted = $date_obj->format('d M Y');
        $english_month = $date_obj->format('M');
        
        if (isset($arabic_months[$english_month])) {
            $formatted = str_replace($english_month, $arabic_months[$english_month], $formatted);
        }
        
        return $formatted;
    } else {
        return $date_obj->format('d M Y');
    }
}
