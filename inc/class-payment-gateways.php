<?php
declare(strict_types = 1);

namespace epiphyt\Limit_Payment_Methods;

use epiphyt\Limit_Payment_Methods\settings\Product;

/**
 * Payment gateways functionality.
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Limit_Payment_Methods
 */
final class Payment_Gateways {
	/**
	 * Initialize functions.
	 */
	public static function init(): void {
		\add_filter( 'woocommerce_available_payment_gateways', [ self::class, 'disable_in_cart' ] );
		\add_filter( 'woocommerce_available_payment_gateways', [ self::class, 'disable_in_order_pay' ] );
	}
	
	/**
	 * Disable limited payment gateway in cart.
	 * 
	 * @param	\WC_Payment_Gateway[]	$gateways List of payment gateways
	 * @return	\WC_Payment_Gateway[] Updated list of payment gateways
	 */
	public static function disable_in_cart( array $gateways ): array {
		if ( ! \is_checkout() ) {
			return $gateways;
		}
		
		/** @var array{data: \WC_Product} $product_item */
		foreach ( \WC()->cart->get_cart_contents() as $product_item ) {
			$disabled_payment_methods = \array_filter( (array) $product_item['data']->get_meta( Product::META_KEY ) );
			
			if ( empty( $disabled_payment_methods ) ) {
				continue;
			}
			
			foreach ( $disabled_payment_methods as $method ) {
				unset( $gateways[ $method ] );
			}
		}
		
		return $gateways;
	}
	
	/**
	 * Disable limited payment gateway in order pay page.
	 * 
	 * @param	\WC_Payment_Gateway[]	$gateways List of payment gateways
	 * @return	\WC_Payment_Gateway[] Updated list of payment gateways
	 */
	public static function disable_in_order_pay( array $gateways ): array {
		if ( ! \is_wc_endpoint_url( 'order-pay' ) ) {
			return $gateways;
		}
		
		$order_id = \wc_get_order_id_by_order_key( \sanitize_text_field( \wp_unslash( $_GET['key'] ?? '' ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$order = \wc_get_order( $order_id );
		
		if ( ! $order instanceof \WC_Order && ! $order instanceof \WC_Order_Refund ) {
			return $gateways;
		}
		
		foreach ( $order->get_items() as $product_item ) {
			$disabled_payment_methods = \array_filter( (array) $product_item->get_meta( Product::META_KEY ) );
			
			if ( empty( $disabled_payment_methods ) ) {
				continue;
			}
			
			foreach ( $disabled_payment_methods as $method ) {
				unset( $gateways[ $method ] );
			}
		}
		
		return $gateways;
	}
}
