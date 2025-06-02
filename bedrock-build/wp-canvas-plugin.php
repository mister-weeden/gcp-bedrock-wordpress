<?php
/**
 * Plugin Name: Canvas LMS Integration
 * Plugin URI: https://example.com/canvas-lms-integration
 * Description: Integrates with Canvas LMS API to create WordPress topics for courses and blog entries for course content.
 * Version: 1.0.0
 * Author: AI-Dock
 * Author URI: https://example.com
 * Text Domain: canvas-lms-integration
 * License: GPL-2.0+
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('CANVAS_LMS_INTEGRATION_VERSION', '1.0.0');
define('CANVAS_LMS_INTEGRATION_PATH', plugin_dir_path(__FILE__));
define('CANVAS_LMS_INTEGRATION_URL', plugin_dir_url(__FILE__));

/**
 * The core plugin class
 */
class Canvas_LMS_Integration {

    /**
     * The single instance of the class
     */
    protected static $_instance = null;

    /**
     * Canvas API URL
     */
    private $api_url = '';

    /**
     * Canvas API token
     */
    private $api_token = '';

    /**
     * Main Plugin Instance
     */
    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor
     */
    public function __construct() {
        // Initialize hooks
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        
        // Add settings link on plugin page
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'add_settings_link'));
        
        // Schedule cron job for syncing
        add_action('canvas_lms_sync_event', array($this, 'sync_canvas_data'));
        
        // Register activation/deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        // Load settings
        $this->api_url = get_option('canvas_lms_api_url', '');
        $this->api_token = get_option('canvas_lms_api_token', '');
        
        // Add shortcodes
        add_shortcode('canvas_courses', array($this, 'courses_shortcode'));
    }

    /**
     * Plugin activation
     */
    public function activate() {
        // Schedule cron job
        if (!wp_next_scheduled('canvas_lms_sync_event')) {
            wp_schedule_event(time(), 'daily', 'canvas_lms_sync_event');
        }
        
        // Create custom post types
        $this->register_post_types();
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Clear scheduled hook
        wp_clear_scheduled_hook('canvas_lms_sync_event');
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Register custom post types
     */
    public function register_post_types() {
        // Register Course post type
        register_post_type('canvas_course', array(
            'labels' => array(
                'name' => __('Canvas Courses', 'canvas-lms-integration'),
                'singular_name' => __('Canvas Course', 'canvas-lms-integration'),
            ),
            'public' => true,
            'has_archive' => true,
            'menu_icon' => 'dashicons-welcome-learn-more',
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
            'rewrite' => array('slug' => 'canvas-courses'),
        ));
        
        // Register Course Content post type
        register_post_type('canvas_content', array(
            'labels' => array(
                'name' => __('Canvas Content', 'canvas-lms-integration'),
                'singular_name' => __('Canvas Content', 'canvas-lms-integration'),
            ),
            'public' => true,
            'has_archive' => true,
            'menu_icon' => 'dashicons-media-document',
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
            'rewrite' => array('slug' => 'canvas-content'),
        ));
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('Canvas LMS Integration', 'canvas-lms-integration'),
            __('Canvas LMS', 'canvas-lms-integration'),
            'manage_options',
            'canvas_lms_integration',
            array($this, 'display_admin_page'),
            'dashicons-welcome-learn-more',
            30
        );
        
        add_submenu_page(
            'canvas_lms_integration',
            __('Settings', 'canvas-lms-integration'),
            __('Settings', 'canvas-lms-integration'),
            'manage_options',
            'canvas_lms_integration',
            array($this, 'display_admin_page')
        );
        
        add_submenu_page(
            'canvas_lms_integration',
            __('Sync Now', 'canvas-lms-integration'),
            __('Sync Now', 'canvas-lms-integration'),
            'manage_options',
            'canvas_lms_sync',
            array($this, 'display_sync_page')
        );
    }

    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('canvas_lms_settings', 'canvas_lms_api_url');
        register_setting('canvas_lms_settings', 'canvas_lms_api_token');
        register_setting('canvas_lms_settings', 'canvas_lms_sync_frequency');
        register_setting('canvas_lms_settings', 'canvas_lms_course_category');
        
        add_settings_section(
            'canvas_lms_settings_section',
            __('Canvas LMS API Settings', 'canvas-lms-integration'),
            array($this, 'settings_section_callback'),
            'canvas_lms_settings'
        );
        
        add_settings_field(
            'canvas_lms_api_url',
            __('Canvas API URL', 'canvas-lms-integration'),
            array($this, 'api_url_field_callback'),
            'canvas_lms_settings',
            'canvas_lms_settings_section'
        );
        
        add_settings_field(
            'canvas_lms_api_token',
            __('Canvas API Token', 'canvas-lms-integration'),
            array($this, 'api_token_field_callback'),
            'canvas_lms_settings',
            'canvas_lms_settings_section'
        );
        
        add_settings_field(
            'canvas_lms_sync_frequency',
            __('Sync Frequency', 'canvas-lms-integration'),
            array($this, 'sync_frequency_field_callback'),
            'canvas_lms_settings',
            'canvas_lms_settings_section'
        );
        
        add_settings_field(
            'canvas_lms_course_category',
            __('Course Category', 'canvas-lms-integration'),
            array($this, 'course_category_field_callback'),
            'canvas_lms_settings',
            'canvas_lms_settings_section'
        );
    }

    /**
     * Settings section callback
     */
    public function settings_section_callback() {
        echo '<p>' . __('Configure your Canvas LMS API settings below. You need to generate an API token from your Canvas account.', 'canvas-lms-integration') . '</p>';
    }

    /**
     * API URL field callback
     */
    public function api_url_field_callback() {
        $api_url = get_option('canvas_lms_api_url', 'https://texastech.instructure.com');
        echo '<input type="text" id="canvas_lms_api_url" name="canvas_lms_api_url" value="' . esc_attr($api_url) . '" class="regular-text" />';
        echo '<p class="description">' . __('Enter your Canvas LMS API URL (e.g., https://texastech.instructure.com)', 'canvas-lms-integration') . '</p>';
    }

    /**
     * API token field callback
     */
    public function api_token_field_callback() {
        $api_token = get_option('canvas_lms_api_token', '');
        echo '<input type="password" id="canvas_lms_api_token" name="canvas_lms_api_token" value="' . esc_attr($api_token) . '" class="regular-text" />';
        echo '<p class="description">' . __('Enter your Canvas LMS API token. You can generate this from your Canvas account settings.', 'canvas-lms-integration') . '</p>';
    }

    /**
     * Sync frequency field callback
     */
    public function sync_frequency_field_callback() {
        $frequency = get_option('canvas_lms_sync_frequency', 'daily');
        $options = array(
            'hourly' => __('Hourly', 'canvas-lms-integration'),
            'twicedaily' => __('Twice Daily', 'canvas-lms-integration'),
            'daily' => __('Daily', 'canvas-lms-integration'),
            'weekly' => __('Weekly', 'canvas-lms-integration'),
        );
        
        echo '<select id="canvas_lms_sync_frequency" name="canvas_lms_sync_frequency">';
        foreach ($options as $key => $label) {
            echo '<option value="' . esc_attr($key) . '" ' . selected($frequency, $key, false) . '>' . esc_html($label) . '</option>';
        }
        echo '</select>';
    }

    /**
     * Course category field callback
     */
    public function course_category_field_callback() {
        $selected_category = get_option('canvas_lms_course_category', 0);
        wp_dropdown_categories(array(
            'name' => 'canvas_lms_course_category',
            'selected' => $selected_category,
            'show_option_none' => __('Select a category', 'canvas-lms-integration'),
            'option_none_value' => '0',
        ));
        echo '<p class="description">' . __('Select the category where course posts will be created.', 'canvas-lms-integration') . '</p>';
    }

    /**
     * Add settings link to plugin page
     */
    public function add_settings_link($links) {
        $settings_link = '<a href="admin.php?page=canvas_lms_integration">' . __('Settings', 'canvas-lms-integration') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        if ('toplevel_page_canvas_lms_integration' !== $hook && 'canvas-lms_page_canvas_lms_sync' !== $hook) {
            return;
        }
        
        wp_enqueue_style('canvas-lms-admin-css', CANVAS_LMS_INTEGRATION_URL . 'assets/css/admin.css', array(), CANVAS_LMS_INTEGRATION_VERSION);
        wp_enqueue_script('canvas-lms-admin-js', CANVAS_LMS_INTEGRATION_URL . 'assets/js/admin.js', array('jquery'), CANVAS_LMS_INTEGRATION_VERSION, true);
        
        wp_localize_script('canvas-lms-admin-js', 'canvasLmsSettings', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('canvas_lms_nonce'),
        ));
    }

    /**
     * Display admin page
     */
    public function display_admin_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <form method="post" action="options.php">
                <?php
                settings_fields('canvas_lms_settings');
                do_settings_sections('canvas_lms_settings');
                submit_button();
                ?>
            </form>
            
            <div class="canvas-lms-card">
                <h2><?php _e('Test Connection', 'canvas-lms-integration'); ?></h2>
                <p><?php _e('Click the button below to test your Canvas API connection:', 'canvas-lms-integration'); ?></p>
                <button id="canvas-lms-test-connection" class="button button-primary">
                    <?php _e('Test Connection', 'canvas-lms-integration'); ?>
                </button>
                <div id="canvas-lms-connection-result" style="margin-top: 15px;"></div>
            </div>
        </div>
        <?php
    }

    /**
     * Display sync page
     */
    public function display_sync_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Sync Canvas LMS Data', 'canvas-lms-integration'); ?></h1>
            
            <div class="canvas-lms-card">
                <h2><?php _e('Manual Sync', 'canvas-lms-integration'); ?></h2>
                <p><?php _e('Click the button below to manually sync data from Canvas LMS:', 'canvas-lms-integration'); ?></p>
                <button id="canvas-lms-sync-now" class="button button-primary">
                    <?php _e('Sync Now', 'canvas-lms-integration'); ?>
                </button>
                <div id="canvas-lms-sync-result" style="margin-top: 15px;"></div>
            </div>
            
            <div class="canvas-lms-card">
                <h2><?php _e('Sync History', 'canvas-lms-integration'); ?></h2>
                <table class="widefat">
                    <thead>
                        <tr>
                            <th><?php _e('Date', 'canvas-lms-integration'); ?></th>
                            <th><?php _e('Courses Synced', 'canvas-lms-integration'); ?></th>
                            <th><?php _e('Content Items Synced', 'canvas-lms-integration'); ?></th>
                            <th><?php _e('Status', 'canvas-lms-integration'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sync_history = get_option('canvas_lms_sync_history', array());
                        if (empty($sync_history)) {
                            echo '<tr><td colspan="4">' . __('No sync history available.', 'canvas-lms-integration') . '</td></tr>';
                        } else {
                            foreach ($sync_history as $history) {
                                echo '<tr>';
                                echo '<td>' . date('Y-m-d H:i:s', $history['time']) . '</td>';
                                echo '<td>' . esc_html($history['courses_count']) . '</td>';
                                echo '<td>' . esc_html($history['content_count']) . '</td>';
                                echo '<td>' . esc_html($history['status']) . '</td>';
                                echo '</tr>';
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }

    /**
     * Sync Canvas data
     */
    public function sync_canvas_data() {
        if (empty($this->api_url) || empty($this->api_token)) {
            return false;
        }
        
        $sync_result = array(
            'time' => time(),
            'courses_count' => 0,
            'content_count' => 0,
            'status' => 'started',
        );
        
        try {
            // Fetch courses
            $courses = $this->fetch_courses();
            
            if (is_wp_error($courses)) {
                $sync_result['status'] = 'error: ' . $courses->get_error_message();
                $this->update_sync_history($sync_result);
                return false;
            }
            
            $sync_result['courses_count'] = count($courses);
            
            // Process each course
            foreach ($courses as $course) {
                $this->process_course($course);
                
                // Fetch and process course modules
                $modules = $this->fetch_course_modules($course->id);
                if (!is_wp_error($modules)) {
                    foreach ($modules as $module) {
                        $this->process_module($module, $course);
                        $sync_result['content_count'] += count($module->items);
                    }
                }
            }
            
            $sync_result['status'] = 'completed';
            $this->update_sync_history($sync_result);
            return true;
            
        } catch (Exception $e) {

