<?php
/**
 * Plugin Name: SMTP Configuration
 * Description: Configure WordPress to use MailHog SMTP server
 * Version: 1.0
 */

// Configure WordPress to use SMTP
add_action('phpmailer_init', function($phpmailer) {
    $phpmailer->isSMTP();
    $phpmailer->Host = 'mailhog';
    $phpmailer->SMTPAuth = false;
    $phpmailer->Port = 1025;
    $phpmailer->From = 'wordpress@harper-corp.com';
    $phpmailer->FromName = 'Harper Corp WordPress';
});
