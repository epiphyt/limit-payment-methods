<?php
declare(strict_types = 1);

namespace epiphyt\Limit_Payment_Methods;

use Automattic\WooCommerce\Utilities\FeaturesUtil;
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
		\add_action( 'before_woocommerce_init', [ self::class, 'set_high_performance_order_storage_compatibility' ] );
		\add_action( 'init', [ self::class, 'load_textdomain' ], 0 );
		\add_action( 'init', [ self::class, 'register_post_meta' ] );
		
		Payment_Gateways::init();
		Product::init();
	}
	
	/**
	 * Load translations.
	 */
	public static function load_textdomain(): void {
		\load_plugin_textdomain( 'limit-payment-methods', false, \dirname( \plugin_basename( \EPI_LIMIT_PAYMENT_METHODS_FILE ) ) . '/languages' );
	}
	
	/**
	 * Register post meta fields.
	 */
	public static function register_post_meta(): void {
		\register_post_meta(
			'product',
			Product::META_KEY,
			[
				'default' => [],
				'description' => \__( 'Disallowed payment methods.', 'limit-payment-methods' ),
				'show_in_rest' => [
					'schema' => [
						'items' => [
							'type' => 'number',
						],
						'type' => 'array',
					],
				],
				'single' => true,
				'type' => 'array',
			]
		);
	}
	
	/**
	 * Set high performance order storage (HPOS) compatibility.
	 */
	public static function set_high_performance_order_storage_compatibility(): void {
		if ( \class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			FeaturesUtil::declare_compatibility( 'custom_order_tables', \EPI_LIMIT_PAYMENT_METHODS_FILE, true );
		}
	}
}
