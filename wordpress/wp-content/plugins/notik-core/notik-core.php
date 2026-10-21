<?php
/**
 * Plugin Name: Notik Core
 * Plugin URI: https://notik.ir
 * Description: قابلیت‌های اختصاصی پلتفرم نوتیک: تأیید فروشنده، بررسی محصول، گزارش تخلف، امنیت دانلود و یکپارچگی واترمارک.
 * Version: 1.0.0
 * Author: Notik Team
 * Text Domain: notik-core
 * Requires at least: 6.0
 * Requires PHP: 8.0
 * License: GPL-2.0-or-later
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'NOTIK_CORE_VERSION', '1.0.0' );
define( 'NOTIK_CORE_FILE', __FILE__ );
define( 'NOTIK_CORE_DIR', plugin_dir_path( __FILE__ ) );
define( 'NOTIK_CORE_URL', plugin_dir_url( __FILE__ ) );

function notik_core_check_deps() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', function() {
			echo '<div class="notice notice-error"><p>افزونه Notik Core نیاز به WooCommerce دارد.</p></div>';
		} );
		return false;
	}
	return true;
}

function notik_core_init() {
	if ( ! notik_core_check_deps() ) {
		return;
	}
	require_once NOTIK_CORE_DIR . 'includes/class-vendor-approval.php';
	require_once NOTIK_CORE_DIR . 'includes/class-product-moderation.php';
	require_once NOTIK_CORE_DIR . 'includes/class-report-content.php';
	require_once NOTIK_CORE_DIR . 'includes/class-download-security.php';
	require_once NOTIK_CORE_DIR . 'includes/class-admin.php';
	Notik_Vendor_Approval::instance();
	Notik_Product_Moderation::instance();
	Notik_Report_Content::instance();
	Notik_Download_Security::instance();
	Notik_Admin::instance();
}
add_action( 'plugins_loaded', 'notik_core_init', 20 );

function notik_core_activate() {
	global $wpdb;
	$charset = $wpdb->get_charset_collate();
	$table   = $wpdb->prefix . 'notik_reports';
	$sql = "CREATE TABLE IF NOT EXISTS $table (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		product_id bigint(20) unsigned NOT NULL,
		reporter_id bigint(20) unsigned DEFAULT NULL,
		reason text NOT NULL,
		status varchar(20) NOT NULL DEFAULT 'pending',
		admin_note text DEFAULT NULL,
		created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		updated_at datetime DEFAULT NULL,
		PRIMARY KEY (id),
		KEY product_id (product_id),
		KEY status (status)
	) $charset;";
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );
	add_option( 'notik_vendor_auto_approve', 0 );
	add_option( 'notik_product_auto_publish', 0 );
	add_option( 'notik_watermark_enabled', 1 );
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'notik_core_activate' );

function notik_core_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'notik_core_deactivate' );
