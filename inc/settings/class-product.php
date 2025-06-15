<?php
declare(strict_types = 1);

namespace epiphyt\Limit_Payment_Methods\settings;

use WC_Product;

/**
 * Product settings.
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Limit_Payment_Methods
 */
final class Product {
	public const META_KEY = 'limit_payment_gateways';
	
	/**
	 * Initialize functions.
	 */
	public static function init(): void {
		\add_action( 'woocommerce_product_options_advanced', [ self::class, 'register' ] );
		\add_action( 'woocommerce_process_product_meta', [ self::class, 'save' ] );
	}
	
	/**
	 * Register product settings.
	 */
	public static function register(): void {
		$payment_methods = \WC()->payment_gateways()->payment_gateways();
		
		if ( ! $payment_methods ) {
			return;
		}
		
		$product = \wc_get_product( \get_the_ID() ?: 0 );
		
		if ( ! $product instanceof WC_Product ) {
			return;
		}
		
		$disabled_payment_methods = (array) $product->get_meta( self::META_KEY );
		
		echo '<div class="options_group">';
		
		foreach ( $payment_methods as $slug => $payment_method ) {
			if ( $payment_method->enabled !== 'yes' ) {
				continue;
			}
			
			\woocommerce_wp_checkbox( [
				'cbvalue' => $slug,
				'id' => "disable_payment_method-{$slug}",
				'label' => \sprintf(
					/* translators: payment method title */
					\__( 'Disable %s', 'limit-payment-methods' ),
					$payment_method->method_title
				),
				'name' => self::META_KEY . '[]',
				'value' => \in_array( $slug, $disabled_payment_methods, true ) ? $slug : '',
			] );
		}
		
		echo '</div>';
	}
	
	/**
	 * Save product settings.
	 * 
	 * @param	int		$product_id Current product ID
	 */
	public static function save( int $product_id ): void {
		$payment_method = ! empty( $_POST[ self::META_KEY ] ) ? \wc_clean( \wp_unslash( $_POST[ self::META_KEY ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$product = \wc_get_product( $product_id );
		
		if ( $product instanceof WC_Product ) {
			$product->update_meta_data( self::META_KEY, $payment_method );
			$product->save();
		}
	}
}
