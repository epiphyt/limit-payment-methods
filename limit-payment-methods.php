<?php
declare(strict_types = 1);

namespace epiphyt\Limit_Payment_Methods;

/*
Plugin Name:		Limit Payment Methods
Description:		Limit usable payment methods for specific products.
Author:				Epiphyt
Author URI:			https://epiph.yt/en/
Version:			1.0.0-dev
License:			GPL2
License URI:		https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:		limit-payment-methods
Domain Path:		/languages
Requires Plugins:	woocommerce

Limit Payment Methods is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Limit Payment Methods is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Limit Payment Methods. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/
\defined( 'ABSPATH' ) || exit;

if ( ! \defined( 'EPI_LIMIT_PAYMENT_METHODS_BASE' ) ) {
	if ( \file_exists( \WP_PLUGIN_DIR . '/limit-payment-methods/' ) ) {
		\define( 'EPI_LIMIT_PAYMENT_METHODS_BASE', \WP_PLUGIN_DIR . '/limit-payment-methods/' );
	}
	else if ( \file_exists( \WPMU_PLUGIN_DIR . '/limit-payment-methods/' ) ) {
		\define( 'EPI_LIMIT_PAYMENT_METHODS_BASE', \WPMU_PLUGIN_DIR . '/limit-payment-methods/' );
	}
	else {
		\define( 'EPI_LIMIT_PAYMENT_METHODS_BASE', \plugin_dir_path( __FILE__ ) );
	}
}

\define( 'EPI_LIMIT_PAYMENT_METHODS_FILE', \EPI_LIMIT_PAYMENT_METHODS_BASE . \basename( __FILE__ ) );
\define( 'EPI_LIMIT_PAYMENT_METHODS_URL', \plugin_dir_url( \EPI_LIMIT_PAYMENT_METHODS_FILE ) );
\define( 'EPI_LIMIT_PAYMENT_METHODS_VERSION', '1.0.0-dev' );

/**
 * Autoload all necessary classes.
 * 
 * @param	string	$class_name The class name of the auto-loaded class
 */
\spl_autoload_register( static function( string $class_name ): void {
	$path = \explode( '\\', $class_name );
	$filename = \str_replace( '_', '-', \strtolower( \array_pop( $path ) ) );
	
	if ( \strpos( $class_name, __NAMESPACE__ ) !== 0 ) {
		return;
	}
	
	$namespace = \strtolower( __NAMESPACE__ . '\\' );
	$class_name = \str_replace(
		[ $namespace, '\\', '_' ],
		[ '', '/', '-' ],
		\strtolower( $class_name )
	);
	$string_position = \strrpos( $class_name, $filename );
	
	if ( $string_position !== false ) {
		$class_name = \substr_replace( $class_name, 'class-' . $filename, $string_position, \strlen( $filename ) );
	}
	
	$maybe_file = __DIR__ . '/inc/' . $class_name . '.php';
	
	if ( \file_exists( $maybe_file ) ) {
		require_once $maybe_file;
	}
} );

\add_action( 'plugins_loaded', [ Plugin::class, 'init' ] );
