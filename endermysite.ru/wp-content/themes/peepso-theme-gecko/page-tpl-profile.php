<?php
/*
  Template Name: User Profile
*/

get_header();

//  Options page settings
$full_width_layout       = get_post_meta(get_proper_ID(), 'gecko-page-full-width', true);
$builder_friendly_layout = get_post_meta(get_proper_ID(), 'gecko-page-builder-friendly', true);
$hide_sidebars           = get_post_meta(get_proper_ID(), 'gecko-page-sidebars', true);

//  Profile options
$focus_area              = get_post_meta(get_proper_ID(), 'gecko-profile-centered-focus', true);

//  Profile layout setting
$profile_layout          = get_post_meta(get_proper_ID(), 'gc_profile_layout', true);

if (empty( $profile_layout )) : $profile_layout = 'default'; endif;

$main_class = "";

if (is_active_sidebar( 'sidebar-left' ) && (!$hide_sidebars || $hide_sidebars == 'right')) {
  $main_class = "main--left";
}

if (is_active_sidebar( 'sidebar-right' ) && (($hide_sidebars == 'left' || !$hide_sidebars))) {
  $main_class = "main--right";
}

if (is_active_sidebar( 'sidebar-left' ) && is_active_sidebar( 'sidebar-right' ) && !$hide_sidebars) {
  $main_class = "main--both";
}

?>

<?php if (class_exists('PeepSo')) {

$PeepSoActivity = PeepSoActivity::get_instance();
$user = PeepSoUser::get_instance(PeepSoProfileShortcode::get_instance()->get_view_user_id());

  if ($user->get_id() == get_current_user_id()) { ?>
  <style>
  .page-template-page-tpl-profile .ps-widget--profile__cover,
  .page-template-page-tpl-profile .ps-badgeos__widget-title,
  .page-template-page-tpl-profile .ps-badgeos__widget,
  .page-template-page-tpl-profile .ps-widget--profile__header {
    display: none;
  }

  .page-template-page-tpl-profile .ps-progress-status {
    margin-top: 0;
  }
  </style>
<?php
  }
}
?>

<div class="profile profile--<?php echo $profile_layout; ?> <?php if ($focus_area) { echo "profile--centered"; } ?> <?php if ($full_width_layout == 1) : echo " profile-page--full"; endif; if ($builder_friendly_layout == 1) : echo " profile-page--builder"; endif; ?>">
  <?php if ($profile_layout == "full" || $profile_layout == "boxed") : ?>
    <?php get_template_part( 'template-parts/peepso/navbar' ); ?>
    <?php get_template_part( 'template-parts/peepso/focus' ); ?>
  <?php endif; ?>

  <div class="main <?php echo $main_class; if ($full_width_layout == 1) : echo " main--full"; endif; if ($builder_friendly_layout == 1) : echo " main--builder"; endif; ?>">
    <?php get_sidebar('left'); ?>

    <div class="content">
      <?php if ( have_posts() ) : ?>

      <?php
      // Start the loop.
      while ( have_posts() ) : the_post();

        the_content();

      // End the loop.
      endwhile;
      endif;
      ?>
    </div>

    <?php get_sidebar('right'); ?>
  </div>
</div>

<?php

get_footer();
