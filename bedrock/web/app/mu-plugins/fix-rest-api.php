<?php
/**
 * Fix REST API URLs
 */
add_filter('rest_url', function($url) {
    return str_replace('https://www.harper-corp.com', 'http://www.harper-corp.com', $url);
}, 10, 1);

add_filter('rest_authentication_errors', function($errors) {
    // For debugging only - allows REST API access without authentication
    return true;
});