  <?php if (! is_page_template( 'page-tpl-landing.php' ) ) : ?>
    <?php get_template_part( 'template-parts/widgets/bottom' ); ?>
  <?php endif; ?>

  <?php
  $hide_widgets = get_post_meta(get_proper_ID(), 'gecko-page-footer-mobile', true);
  $hide_footer = get_post_meta(get_proper_ID(), 'gecko-page-hide-footer', true);

  if ($hide_widgets == 1) :
  ?>
  <style>
  @media screen and (max-width: 980px) {
    .footer__wrapper {
      display: none;
    }
  }
  </style>
  <?php endif; ?>

  <?php if(get_theme_mod('general_scroll_to_top', '1') == "1") : ?>
  <a href="#body" class="button button--scroll-top js-scroll-top"><i class="gc-icon-angle-up"></i></a>
  <?php endif; ?>

  <?php if (! is_page_template( 'page-tpl-landing.php' ) ) : ?>
    <?php if(! $hide_footer) : ?>
    <footer class="footer">
      <?php if ( is_active_sidebar( 'footer-widgets' ) ) : ?>
      <div class="footer__wrapper">
        <div class="footer__grid">
          <?php dynamic_sidebar( 'footer-widgets' ); ?>
        </div>
      </div>
      <?php endif; ?>

      <div class="footer__bottom">
        <div class="footer__wrapper">
          <div class="footer__copyrights">
            <?php echo get_theme_mod('footer_copyrights', (function_exists('default_gecko_copyright') ? default_gecko_copyright() : '') ); ?>
          </div>

          <div class="footer__menu"><?php wp_nav_menu( array( 'theme_location' => 'footer-menu', 'items_wrap' => '%3$s', 'container' => false, 'fallback_cb' => false ) ); ?></div>

          <?php if ( is_active_sidebar( 'footer-social' ) ) : ?>
          <div class="footer__social">
            <?php dynamic_sidebar( 'footer-social' ); ?>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </footer>
    <?php endif; ?>
  <?php endif; ?>

  <?php wp_footer(); ?>

  <?php echo get_theme_mod('js_foot', ''); ?>
  </body>
</html>
