<?php
declare(strict_types = 1);

namespace epiphyt\Limit_Payment_Methods;

use epiphyt\Limit_Payment_Methods\settings\Product;

/**
 * The main plugin class.
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Limit_Payment_Methods
 */
final class Plugin {
	/**
	 * Initialize functions.
	 */
	public static function init(): void {
		\add_action( 'init', [ self::class, 'load_textdomain' ], 0 );
		
		Payment_Gateways::init();
		Product::init();
	}
	
	/**
	 * Load translations.
	 */
	public static function load_textdomain(): void {
		\load_plugin_textdomain( 'limit-payment-methods', false, \dirname( \plugin_basename( \EPI_LIMIT_PAYMENT_METHODS_FILE ) ) . '/languages' );
	}
}
