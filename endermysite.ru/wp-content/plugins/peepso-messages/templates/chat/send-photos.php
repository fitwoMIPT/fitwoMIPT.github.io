<div class="ps-conversation-item my-message my-message-photos-{id}">
    <div class="ps-conversation-body">
        <div class="ps-conversation-content"></div>
        <div class="ps-conversation-attachment">
            <div class="ps-clearfix">{item}<a class="ps-conversation-photo-item ps-conversation-photo-placeholder"><img src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>" alt="" /></a>{/item}</div>
        </div>
        <div class="ps-conversation-time">
            <small>
                <span><?php echo __('just now', 'msgso'); ?></span>
                <span class="ps-icon-ok"></span>
            </small>
        </div>
    </div>
</div>
