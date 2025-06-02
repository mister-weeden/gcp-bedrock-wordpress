<?php
/**
 * Fix WordPress REST API URL issues
 * This script updates the REST API URLs in the database to use HTTP instead of HTTPS
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

// Update the home and siteurl options
$mysqli->query("UPDATE wp_options SET option_value = 'http://www.harper-corp.com' WHERE option_name = 'home'");
$mysqli->query("UPDATE wp_options SET option_value = 'http://www.harper-corp.com/wp' WHERE option_name = 'siteurl'");

echo "Updated home and siteurl options.\n";

// Find and update any REST API URLs in post content
$mysqli->query("UPDATE wp_posts SET post_content = REPLACE(post_content, 'https://www.harper-corp.com', 'http://www.harper-corp.com')");

echo "Updated REST API URLs in post content.\n";

// Find and update any REST API URLs in post meta
$mysqli->query("UPDATE wp_postmeta SET meta_value = REPLACE(meta_value, 'https://www.harper-corp.com', 'http://www.harper-corp.com') WHERE meta_value LIKE '%https://www.harper-corp.com%'");

echo "Updated REST API URLs in post meta.\n";

// Close the connection
$mysqli->close();

echo "REST API URL fix completed.\n";
