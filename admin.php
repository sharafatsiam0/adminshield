<?php

defined('ABSPATH') || exit; 


class admin_AdminShield {
    function __construct(){
        add_action('admin_menu', [$this, 'as_admin__PAGE']);
        add_action('admin_init', [$this, 'adminshield_register_settings']);
    }

    function adminshield_register_settings(){
        register_setting('adminshield_settings', 'adminshield_site_key',   ['sanitize_callback' => 'sanitize_text_field']);
        register_setting('adminshield_settings', 'adminshield_secret_key', ['sanitize_callback' => 'sanitize_text_field']);
        register_setting('adminshield_settings', 'adminshield_theme',      ['sanitize_callback' => 'sanitize_text_field']);
    }

    function as_admin__PAGE() {
        add_menu_page(
            'AdminShield Settings',
            'AdminShield',
            'manage_options',
            'AdminShield_menu',
            [$this, 'AdminShield_callback'],
            'dashicons-admin-generic',
            6
        );
    }

    function AdminShield_callback() {
        // ✅ Capability check — block non-admins accessing this page directly
        if (!current_user_can('manage_options')) {
            wp_die('You do not have permission to access this page.');
        }

        // ✅ Manual nonce verification on form submission
        if (isset($_POST['adminshield_nonce'])) {
            if (!wp_verify_nonce($_POST['adminshield_nonce'], 'adminshield_save_settings')) {
                wp_die('Security check failed. Please try again.');
            }
        }
        ?>
        <div class="wrap">
            <h1>AdminShield Settings</h1>
            <form method="post" action="options.php">

                <?php
                // ✅ Settings API nonce (auto-verified by options.php)
                settings_fields('adminshield_settings');

                // ✅ Manual nonce field (extra layer of verification)
                wp_nonce_field('adminshield_save_settings', 'adminshield_nonce');
                ?>

                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="adminshield_site_key">Cloudflare Site Key</label>
                            </th>
                            <td>
                                <input type="text"
                                    id="adminshield_site_key"
                                    name="adminshield_site_key"
                                    value="<?php echo esc_attr(get_option('adminshield_site_key')); ?>"
                                    class="regular-text">
                                <p class="description">Enter your Cloudflare Turnstile Site Key.</p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="adminshield_secret_key">Cloudflare Secret Key</label>
                            </th>
                            <td>
                                <input type="password"
                                    id="adminshield_secret_key"
                                    name="adminshield_secret_key"
                                    value="<?php echo esc_attr(get_option('adminshield_secret_key')); ?>"
                                    class="regular-text"
                                    autocomplete="new-password">
                                <p class="description">Enter your Cloudflare Turnstile Secret Key.</p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="adminshield_theme">Turnstile Theme</label>
                            </th>
                            <td>
                                <select name="adminshield_theme" id="adminshield_theme">
                                    <option value="light" <?php selected(get_option('adminshield_theme', 'light'), 'light'); ?>>Light</option>
                                    <option value="dark"  <?php selected(get_option('adminshield_theme', 'light'), 'dark');  ?>>Dark</option>
                                    <option value="auto"  <?php selected(get_option('adminshield_theme', 'light'), 'auto');  ?>>Auto</option>
                                </select>
                                <p class="description">Choose how Turnstile should appear on login page.</p>
                            </td>
                        </tr>

                    </tbody>
                </table>

                <?php submit_button(); ?>

            </form>
        </div>
        <?php
    }
}

new admin_AdminShield();