<?php
/*
Plugin Name: AdminShield Turnstile
Description: Adds Cloudflare Turnstile to the WordPress login page to prevent bots.
Version: 1.0
Author: Sharafat Siam
License: GPLv2 or later
Text Domain: adminshield
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages

*/

defined('ABSPATH') || exit; // Prevent direct access

// Define plugin constants
define('ASTP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ASTP_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include admin options (if you have admin.php for settings)
if (file_exists(ASTP_PLUGIN_DIR . 'admin.php')) {
    require_once ASTP_PLUGIN_DIR . 'admin.php';
}

class ASTP_Main {

    public function __construct() {
        add_action('login_enqueue_scripts', [$this, 'enqueue_login_assets']);
        add_action('login_form', [$this, 'display_turnstile']);
        add_filter('authenticate', [$this, 'verify_human'], 20, 3);
    }

    /**
     * Enqueue CSS and JS for login page
     */
    public function enqueue_login_assets() {
        wp_enqueue_style(
            'astp-login-css',
            ASTP_PLUGIN_URL . 'assets/css/style.css',
            [],
            '1.0.0',
            'all'
        );

        wp_enqueue_script(
        'cf-turnstile',
        'https://challenges.cloudflare.com/turnstile/v0/api.js',
        [],
        null,
        true
    );
    }

    /**
     * Display Cloudflare Turnstile on login form
     */
    public function display_turnstile() {
        $site_key = esc_attr(get_option('adminshield_site_key'));
        $theme    = esc_attr(get_option('adminshield_theme', 'light'));
        ?>
        <div class="wp-wrap">
            <div class="astp-turnstile-wrapper">
            <div class="cf-turnstile"   data-size="flexible"
                 data-sitekey="<?php echo esc_attr($site_key) ; ?>"
                 data-theme="<?php echo esc_attr($theme) ; ?>">
            </div>
        </div>
        </div>
        
        <?php
    }

    /**
     * Verify Turnstile response
     */
    public function verify_human($user, $username, $password) {
        if (empty($_POST['cf-turnstile-response'])) {
            return new WP_Error('turnstile_missing', __('Please verify you are human.', 'adminshield'));
        }

        $response = wp_remote_post(
            'https://challenges.cloudflare.com/turnstile/v0/siteverify',
            [
                'body' => [
                    'secret'   => get_option('adminshield_secret_key'),
                    'response' => sanitize_text_field($_POST['cf-turnstile-response']),
                    'remoteip' => filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP),
                ],
            ]
        );

        if (is_wp_error($response)) {
            return new WP_Error('turnstile_error', __('Verification failed.', 'adminshield'));
        }

        $result = json_decode(wp_remote_retrieve_body($response), true);

        if (empty($result['success']) || $result['success'] !== true) {
            return new WP_Error('turnstile_failed', __('Turnstile verification failed.', 'adminshield'));
        }

        return $user;
    }
}

// Initialize the plugin
new ASTP_Main();
