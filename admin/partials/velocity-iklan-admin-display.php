<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://velocitydeveloper.com
 * @since      1.0.0
 *
 * @package    Velocity_Iklan
 * @subpackage Velocity_Iklan/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) exit;



add_action('admin_menu', 'velocity_iklan_custom_submenu');
function velocity_iklan_custom_submenu() {
    add_submenu_page('edit.php?post_type=iklan', 'Dashboard Iklan', 'Dashboard Iklan', 'manage_options', 'iklan_dashboard', 'velocity_iklan_dashboard');
}


function velocity_iklan_dashboard(){
    // Tambahkan konten submenu di sini
    echo '<div class="wrap">';
        echo '<h2>Dashboard Iklan</h2>';
        require_once VELOCITY_IKLAN_PLUGIN_DIR . 'admin/partials/admin-dashboard.php';
    echo '</div>';
}
