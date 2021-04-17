<?php
if (!is_active_sidebar( 'top-widgets' )) { return false; }

if ( is_active_sidebar( 'top-widgets' ) ) : ?>
<div class="widgets widgets--top">
    <div class="widgets__wrapper">
      <div class="widgets__grid">
        <?php dynamic_sidebar( 'top-widgets' ); ?>
      </div>
    </div>
</div>
<?php endif; ?>
