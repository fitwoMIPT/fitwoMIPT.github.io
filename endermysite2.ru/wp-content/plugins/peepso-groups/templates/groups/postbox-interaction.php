<?php

$random = rand();

?><div class="ps-dropdown__menu ps-dropdown__menu--postbox-group ps-js-postbox-group" style="display:none">
    <a role="menuitem" class="ps-dropdown__group" data-option-value="">
        <div class="ps-checkbox ps-dropdown__group-title">
            <input type="radio" name="peepso_postbox_group_<?php echo $random ?>"
                id="peepso_postbox_group_<?php echo $random ?>_" value="" checked="checked" />
            <label for="peepso_postbox_group_<?php echo $random ?>_">
                <span><?php echo __('My profile', 'groupso') ?></span>
            </label>
        </div>
    </a>
    <a role="menuitem" class="ps-dropdown__group" data-option-value="group">
        <div class="ps-checkbox ps-dropdown__group-title">
            <input type="radio" name="peepso_postbox_group_<?php echo $random ?>"
                id="peepso_postbox_group_<?php echo $random ?>_group" value="group" />
            <label for="peepso_postbox_group_<?php echo $random ?>_group">
                <span><?php echo __('A group', 'groupso') ?></span>
            </label>
        </div>
        <div class="ps-postbox__schedule ps-js-group-finder" style="margin-top:10px">
            <div class="ps-postbox__groups">
                <input type="text" class="ps-input" name="query" value=""
                    placeholder="<?php echo __('Start typing to search...', 'groupso'); ?>" />
                <div class="ps-postbox__groups-box ps-js-result">
                    <ul class="ps-postbox__groups-list"></ul>
                    <script type="text/template" class="ps-js-result-item-template">
                        <li class="ps-postbox__groups-item" data-id="{{= data.id }}" data-name="{{= data.name }}">
                            <span>
                                <span class="ps-postbox__groups-item-name">{{= data.name }}</span>
                                {{ if ( data.privacy ) { }}
                                <span class="ps-postbox__groups-item-privacy">
                                    <i class="{{= data.privacy.icon }}"></i>
                                    {{= data.privacy.name }}
                                </span>
                                {{ } }}
                            </span>
                            <p>{{= data.description || '&nbsp;' }}</p>
                        </li>
                    </script>
                </div>
                <div class="ps-loading ps-js-loading" style="display:none">
                    <img src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>" alt="" />
                </div>
            </div>
        </div>
    </a>
</div>
