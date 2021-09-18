<?php

defined( 'ABSPATH' ) or die( );
//preg_match('/^(.+)wp-content\/.*/', dirname(__FILE__), $path);
//include($path[1] . 'wp-load.php');



function settings_display(){

    $user = wp_get_current_user();
    $html = get_avatar($user->ID);

    $line_of_work = get_user_meta($user->ID, 'line_of_work', true);
    $qualification = get_user_meta($user->ID, 'qualification', true);
    $country = get_user_meta($user->ID, 'country', true);

    $html .= '<form id="update_settings" class="lForm" action="" method="post">
                    <input id="up_fname" type="text" placeholder="'.pll__('First name').'" value="'.$user->first_name.'" disabled>
                    <input id="up_lname" type="text" placeholder="'.pll__('Last name').'" value="'.$user->last_name.'" disabled>
                    <input id="up_email" class="input_disabled" type="text" placeholder="'.pll__('Email').'" value="'.$user->user_email.'" disabled>
                    <input id="up_Username" class="input_disabled" type="text" placeholder="'.pll__('Username').'" value="'.$user->user_nicename.'" disabled>
                    <input id="up_organization" type="text" placeholder="'.pll__('Organization').'" value="'.get_user_meta($user->ID, 'organization', true).'">
                    <select id="up_country">
                        <option value="Germany" '.($country == "Germany" ? "selected" : "").'>'.pll__('Germany').'</option>
                        <option value="Italy" '.($country == "Italy" ? "selected" : "").'>'.pll__('Italy').'</option>
                        <option value="Malta" '.($country == "Malta" ? "selected" : "").'>'.pll__('Malta').'</option>
                        <option value="Romania" '.($country == "Romania" ? "selected" : "").'>'.pll__('Romania').'</option>
                        <option value="Turkey" '.($country == "Turkey" ? "selected" : "").'>'.pll__('Turkey').'</option>
                        <option value="Portugal" '.($country == "Portugal" ? "selected" : "").'>'.pll__('Portugal').'</option>
                        <option value="Other" '.($country == "Other" ? "selected" : "").'>'.pll__('Other').'</option>
                    </select>
                    <select id="up_line_of_work">
                        <option value="Security" '.($line_of_work == "Security" ? "selected" : "").'>'.pll__('Security').'</option>
                        <option value="Medical" '.($line_of_work == "Medical" ? "selected" : "").'>'.pll__('Medical').'</option>
                        <option value="Education" '.($line_of_work == "Education" ? "selected" : "").'>'.pll__('Education').'</option>
                        <option value="Psichology" '.($line_of_work == "Psichology" ? "selected" : "").'>'.pll__('Psichology').'</option>
                        <option value="Administrative" '.($line_of_work == "Administrative" ? "selected" : "").'>'.pll__('Administrative').'</option>
                        <option value="Other" '.($line_of_work == "Other" ? "selected" : "").'>'.pll__('Other').'</option>
                    </select>
                    <select id="up_qualification">
                        <option value="Junior" '.($qualification == "Junior" ? "selected" : "").'>'.pll__('Junior').'</option>
                        <option value="Senior" '.($qualification == "Senior" ? "selected" : "").'>'.pll__('Senior').'</option>
                        <option value="Management" '.($qualification == "Management" ? "selected" : "").'>'.pll__('Management').'</option>
                        <option value="Top management" '.($qualification == "Top management" ? "selected" : "").'>'.pll__('Top management').'</option>
                        <option value="Other" '.($qualification == "Other" ? "selected" : "").'>'.pll__('Other').'</option>
                    </select><br>                    
                    <p id="up_status"></p><br>
                    <input id="login_btn" class="lbutton settings_btn" type="button" onclick="" value="'.pll__('Update settings').'"/>
                    '.wp_nonce_field( 'ajax-update_settings-nonce', 'update_settings_security' ).'                    
                </form>';



    return $html;
}
add_shortcode( 'user_settings', 'settings_display');

add_action( 'wp_ajax_update_user_settings', 'update_user_settings');

function update_user_settings(){

    check_ajax_referer('ajax-login-nonce', 'MCsecurity', false);
    $nonce = isset($_REQUEST['MCsecurity']) ? sanitize_text_field(wp_unslash($_REQUEST['MCsecurity'])) : false;

    $flag = wp_verify_nonce($nonce, 'ajax-update_settings-nonce');
    if (true != $flag || is_wp_error($flag)) {
        //TODO Change __('');
        wp_send_json_error(
            array(
                'message' => __('Nonce error, please reload.', 'user-registration'),
            )
        );
    }


    $organization = $_POST['organization'];
    $line_of_work = $_POST['line_of_work'];
    $country = $_POST['country'];
    $qualification = $_POST['qualification'];

    $ID = get_current_user_id();

    update_user_meta($ID, 'line_of_work', $line_of_work);
    update_user_meta($ID, 'organization', $organization);
    update_user_meta($ID, 'qualification', $qualification);
    update_user_meta($ID, 'country', $country);



    wp_send_json_success(array('message' => "Information updated"));
    wp_die();
}
