<?php
/**
 * Recruitment Notification Daily Cron Job
 * 
 * Add this to your server's crontab to run at midnight every day:
 * 0 0 * * * curl -s "https://yoursite.com/wp-content/themes/omsar/inc/recruitment-notify-cron.php" > /dev/null 2>&1
 * 
 * Replace "yoursite.com" with your actual domain.
 */

// Load WordPress
// Go up 4 levels: inc -> omsar -> themes -> wp-content -> root
require_once(dirname(__FILE__) . '/../../../../wp-load.php');

// Run the notification check
if (function_exists('omsar_check_recruitments_and_notify')) {
    $notified_count = omsar_check_recruitments_and_notify();
    
    if (php_sapi_name() === 'cli') {
        echo "Recruitment notification cron completed. Processed: $notified_count recruitments\n";
    } else {
        echo "Recruitment notification cron completed. Processed: $notified_count recruitments";
    }
} else {
    die('Error: Function not found');
}
