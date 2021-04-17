<div class="peepso">
  <?php PeepSoTemplate::exec_template('general','navbar'); ?>

  <div class="ps-page ps-page--messages">
    <div class="ps-messages">
        <?php wp_nonce_field('load-messages', '_messages_nonce'); ?>
        <div class="ps-messages__inbox" id="inbox"></div>
    </div>
  </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function() {
        ps_messages.load_messages('inbox', 1);
    });
</script>
<?php PeepSoTemplate::exec_template('activity', 'dialogs'); ?>
