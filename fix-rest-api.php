<?php
/**
 * Fix WordPress REST API issues
 * This script adds necessary configuration to WordPress to fix REST API issues
 */

// Define the path to the wp-config.php file
$wp_config_path = '/srv/bedrock/web/wp-config.php';

// Read the current content
$content = file_get_contents($wp_config_path);

// Check if our fix is already in place
if (strpos($content, 'WP_HOME') === false) {
    // Add our configuration before the "require_once ABSPATH" line
    $new_content = str_replace(
        "require_once ABSPATH . 'wp-settings.php';",
        "// Fix for REST API\ndefine('WP_HOME', 'http://www.harper-corp.com');\ndefine('WP_SITEURL', 'http://www.harper-corp.com/wp');\n\nrequire_once ABSPATH . 'wp-settings.php';",
        $content
    );
    
    // Write the updated content back to the file
    file_put_contents($wp_config_path, $new_content);
    echo "WordPress configuration updated to fix REST API issues.\n";
} else {
    echo "WordPress configuration already contains the necessary settings.\n";
}

// Add a fix for the REST API URL
$htaccess_path = '/srv/bedrock/web/.htaccess';
$htaccess_content = '';

if (file_exists($htaccess_path)) {
    $htaccess_content = file_get_contents($htaccess_path);
}

if (strpos($htaccess_content, 'RewriteRule ^index\.php') === false) {
    $htaccess_content .= "\n# BEGIN WordPress\n";
    $htaccess_content .= "<IfModule mod_rewrite.c>\n";
    $htaccess_content .= "RewriteEngine On\n";
    $htaccess_content .= "RewriteBase /\n";
    $htaccess_content .= "RewriteRule ^index\.php$ - [L]\n";
    $htaccess_content .= "RewriteCond %{REQUEST_FILENAME} !-f\n";
    $htaccess_content .= "RewriteCond %{REQUEST_FILENAME} !-d\n";
    $htaccess_content .= "RewriteRule . /index.php [L]\n";
    $htaccess_content .= "</IfModule>\n";
    $htaccess_content .= "# END WordPress\n";
    
    file_put_contents($htaccess_path, $htaccess_content);
    echo "Created .htaccess file with WordPress rewrite rules.\n";
} else {
    echo ".htaccess file already contains WordPress rewrite rules.\n";
}

echo "REST API fix completed.\n";
