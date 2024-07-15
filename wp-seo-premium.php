<?php
/**
 * Yoast SEO Plugin.
 *
 * WPSEO Premium plugin file.
 *
 * @package   WPSEO\Main
 * @copyright Copyright (C) 2008-2024, Yoast BV - support@yoast.com
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 *
 * @wordpress-plugin
 * Plugin Name: Yoast SEO Premium
 * Version:     23.0
 * Plugin URI:  [D4RK P3D]
 * Description: The first true all-in-one SEO solution for WordPress, including on-page content analysis, XML sitemaps and much more.
 * Author:      https://pedralizad.ir
 * Author URI:  https://pedralizad.ir
 * Text Domain: wordpress-seo-premium
 * Domain Path: /languages/
 * License:     GPL v3
 * Requires at least: 6.4
 * Requires PHP: 7.2.5
 * Requires Yoast SEO: 23.0
 *
 * WC requires at least: 7.1
 * WC tested up to: 9.0
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

use Yoast\WP\SEO\Premium\Addon_Installer;
$site_information = get_transient( 'wpseo_site_information' );
if ( isset( $site_information->subscriptions ) && ( count( $site_information->subscriptions ) == 0 ) ) {
delete_transient( 'wpseo_site_information' );
delete_transient( 'wpseo_site_information_quick' );
}

add_filter( 'pre_http_request', function( $pre, $parsed_args, $url ){
$site_information = (object) [
'url' => NULL,
'subscriptions' => []
];

$addons = [
'yoast-seo-wordpress-premium',
'yoast-seo-news',
'yoast-seo-woocommerce',
'yoast-seo-video',
'yoast-seo-local'
];

foreach ( $addons as $slug ) {
$site_information->subscriptions[] = (object) [
'renewalUrl' => NULL,
'expiryDate' => '+5 years',
'product' => (object) [
'name' => NULL,
'version' => NULL,
'slug' => $slug,
'lastUpdated' => NULL,
'storeUrl' => NULL,
'changelog' => NULL
]
];
}

if ( strpos( $url, 'https://my.yoast.com/api/sites/current' ) !== false ) {
return [
'response' => [ 'code' => 200, 'message' => 'ОК' ],
'body' => json_encode( $site_information )
];
} else {
return $pre;
}
}, 10, 3 );
if ( ! defined( 'WPSEO_PREMIUM_FILE' ) ) {
	define( 'WPSEO_PREMIUM_FILE', __FILE__ );
}

if ( ! defined( 'WPSEO_PREMIUM_PATH' ) ) {
	define( 'WPSEO_PREMIUM_PATH', plugin_dir_path( WPSEO_PREMIUM_FILE ) );
}

if ( ! defined( 'WPSEO_PREMIUM_BASENAME' ) ) {
	define( 'WPSEO_PREMIUM_BASENAME', plugin_basename( WPSEO_PREMIUM_FILE ) );
}

/**
 * {@internal Nobody should be able to overrule the real version number as this can cause
 *            serious issues with the options, so no if ( ! defined() ).}}
 */
define( 'WPSEO_PREMIUM_VERSION', '23.0' );

// Initialize Premium autoloader.
$wpseo_premium_dir               = WPSEO_PREMIUM_PATH;
$yoast_seo_premium_autoload_file = $wpseo_premium_dir . 'vendor/autoload.php';

if ( is_readable( $yoast_seo_premium_autoload_file ) ) {
	require $yoast_seo_premium_autoload_file;
}

// This class has to exist outside of the container as the container requires Yoast SEO to exist.
$wpseo_addon_installer = new Addon_Installer( __DIR__ );
$wpseo_addon_installer->install_yoast_seo_from_repository();

// Load the container.
if ( ! wp_installing() ) {
	require_once __DIR__ . '/src/functions.php';
	YoastSEOPremium();
}

register_activation_hook( WPSEO_PREMIUM_FILE, [ 'WPSEO_Premium', 'install' ] );
