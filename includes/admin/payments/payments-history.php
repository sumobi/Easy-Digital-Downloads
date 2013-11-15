<?php
/**
 * Admin Payment History
 *
 * @package     EDD
 * @subpackage  Admin/Payments
 * @copyright   Copyright (c) 2013, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Payment History Page
 *
 * Renders the payment history page contents.
 *
 * @access      private
 * @since       1.0
 * @return      void
*/
function edd_payment_history_page() {
	global $edd_options;

	if ( isset( $_GET['view'] ) && 'edit-payment' == $_GET['view'] ) {
		require_once EDD_PLUGIN_DIR . 'includes/admin/payments/edit-payment.php';
	} elseif ( isset( $_GET['view'] ) && 'view-order-details' == $_GET['view'] ) {
		require_once EDD_PLUGIN_DIR . 'includes/admin/payments/view-order-details.php';
	} else {
		require_once EDD_PLUGIN_DIR . 'includes/admin/payments/class-payments-table.php';
		$payments_table = new EDD_Payment_History_Table();
		$payments_table->prepare_items();
	?>
	<div class="wrap">
		<h2><?php _e( 'Payment History', 'edd' ); ?></h2>
		<?php do_action( 'edd_payments_page_top' ); ?>
		<form id="edd-payments-filter" method="get" action="<?php echo admin_url( 'edit.php?post_type=download&page=edd-payment-history' ); ?>">
			<?php $payments_table->search_box( __( 'Search', 'edd' ), 'edd-payments' ); ?>

			<input type="hidden" name="post_type" value="download" />
			<input type="hidden" name="page" value="edd-payment-history" />

			<?php $payments_table->views() ?>

			<?php $payments_table->display() ?>
		</form>
		<?php do_action( 'edd_payments_page_bottom' ); ?>

		<?php if ( apply_filters( 'edd_mobile_link', true ) ) : ?>
		<p class="edd-mobile-link">
			<a href="https://easydigitaldownloads.com/extension/ios-sales-earnings-tracker/" target="_blank">
				<img src="<?php echo EDD_PLUGIN_URL . 'assets/images/icons/iphone.png'; ?>"/>
				<?php _e( 'Get the EDD Sales / Earnings tracker for iOS', 'edd' ); ?>
			</a>
		</p>
		<?php endif; ?>
	</div>
<?php
	}
}

/**
 * Payment History admin titles
 *
 * @since 1.6
 *
 * @param $admin_title
 * @param $title
 * @return string
 */
function edd_view_order_details_title( $admin_title, $title ) {
	if ( 'download_page_edd-payment-history' != get_current_screen()->base )
		return $admin_title;

	if( ! isset( $_GET['edd-action'] ) )
		return $admin_title;

	switch( $_GET['edd-action'] ) :

		case 'view-order-details' :
			$title = __( 'View Order Details', 'edd' ) . ' - ' . $admin_title;
			break;
		case 'edit-payment' :
			$title = __( 'Edit Payment', 'edd' ) . ' - ' . $admin_title;
			break;
		default:
			$title = $admin_title;
			break;
	endswitch;

	return $title;
}
add_filter( 'admin_title', 'edd_view_order_details_title', 10, 2 );

/**
 * Intercept default Edit post links for EDD payments and rewrite them to the View Order Details screen
 *
 * @since 1.8.3
 *
 * @param $url
 * @param $post_id
 * @param $context
 * @return string
 */
function edd_override_edit_post_for_payment_link( $url, $post_id = 0, $context ) {

	$post = get_post( $post_id );
	if( ! $post )
		return $url;

	if( 'edd_payment' != $post->post_type )
		return $url;

	$url = admin_url( 'edit.php?post_type=download&page=edd-payment-history&view=view-order-details&id=' . $post_id );

	return $url;
}
add_filter( 'get_edit_post_link', 'edd_override_edit_post_for_payment_link', 10, 3 );