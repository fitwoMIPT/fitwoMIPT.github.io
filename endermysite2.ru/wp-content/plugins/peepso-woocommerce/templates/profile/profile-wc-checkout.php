<?php
$user = PeepSoUser::get_instance(PeepSoProfileShortcode::get_instance()->get_view_user_id());

$can_edit = FALSE;
if($user->get_id() == get_current_user_id() || current_user_can('edit_users')) {
	$can_edit = TRUE;
}

?>

		<div class="peepso ps-page-profile">
			<?php PeepSoTemplate::exec_template( 'general', 'navbar' ); ?>
			<?php PeepSoTemplate::exec_template( 'profile', 'focus', array( 'current' => WBPWI_Woo_Cart_Tab_Manager::$menu_slug ) ); ?>
			<section id="mainbody" class="ps-page-unstyled">
				<section id="component" role="article" class="clearfix">
					<?php if($can_edit) { PeepSoTemplate::exec_template('profile', 'profile-wc-cart-tabs', array('tabs'=>$tabs, 'current_tab'=> WBPWI_Woo_Cart_Tab_Manager::$menu_slug_checkout));} ?>

					<div class="wbpwi-peepo-woo-wrapper">
						<?php wc_print_notices(); ?>
						<?php
						if ( is_page( wc_get_page_id( 'checkout' ) ) && WC()->cart->is_empty() && empty( $wp->query_vars['order-pay'] ) && ! isset( $wp->query_vars['order-received'] ) ) {
							echo do_shortcode( '[woocommerce_cart]' );
						} else {
							echo do_shortcode( '[woocommerce_checkout]' );
						}
						?>
						</div>
					</section><!--end component-->
				</section><!--end mainbody-->
			</div><!--end row-->