<?php
if (!is_active_sidebar( 'bottom-widgets' )) { return false; }

if ( is_active_sidebar( 'bottom-widgets' ) ) : ?>
<div class="widgets widgets--bottom">
    <div class="widgets__wrapper">
      <div class="widgets__grid">
        <?php dynamic_sidebar( 'bottom-widgets' ); ?>
      </div>
    </div>
</div>
<?php endif; ?>
