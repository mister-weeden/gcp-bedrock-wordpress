<?php
/**
 * Plugin Name: Disable REST API Authentication
 * Description: Disables authentication for the REST API for testing purposes
 * Version: 1.0
 * Author: System
 */

// Remove REST API authentication for testing
add_filter('rest_authentication_errors', function($errors) {
    return null; // Allow access to REST API without authentication
});

// Fix REST API URLs to use HTTP instead of HTTPS
add_filter('rest_url', function($url) {
    return str_replace('https://', 'http://', $url);
});

// Fix site URL in REST API responses
add_filter('rest_url_prefix', function($prefix) {
    return $prefix;
});

// Fix REST API base URL
add_filter('rest_base_url', function($url) {
    return str_replace('https://', 'http://', $url);
});