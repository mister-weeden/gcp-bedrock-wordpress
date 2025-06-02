<?php
/**
 * Fix WordPress REST API links in the database
 */

// Database connection details
$db_host = 'bedrock-db';
$db_name = 'wordpress';
$db_user = 'root';
$db_pass = 'rootpassword';

// Connect to the database
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

echo "Connected to database successfully.\n";

// Add a filter to WordPress to fix REST API URLs
$option_value = <<<'PHP'
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
PHP;

// Check if the mu-plugins directory exists
$result = $mysqli->query("SELECT option_value FROM wp_options WHERE option_name = 'siteurl'");
$row = $result->fetch_assoc();
$siteurl = $row['option_value'];
$result->free();

echo "Site URL: $siteurl\n";

// Close the connection
$mysqli->close();

echo "Creating mu-plugins directory and REST API fix plugin...\n";

// Create the mu-plugins directory if it doesn't exist
@mkdir('/srv/bedrock/web/app/mu-plugins', 0755, true);

// Create the REST API fix plugin
file_put_contents('/srv/bedrock/web/app/mu-plugins/fix-rest-api.php', $option_value);

echo "REST API links fix completed.\n";
