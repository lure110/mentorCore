<?php
/**
 * @package MentorCore
 */
/*
Plugin Name: Mentor Core
Description: Mentor core is used to make roles, profiles and chats for 2 sided connections. Requires Polylang to use multilang translations
Version: 1.0.1
Author: Arnas Abromavičius
Requires PHP: 7.2
License: GPLv2 or later
Text Domain: mentorCore
*/

defined( 'ABSPATH' ) or die( 'Wrong place buddy' );

preg_match('/^(.+)wp-content\/.*/', dirname(__FILE__), $path);
include($path[1] . 'wp-load.php');

// Including files

$plugin_dir = ABSPATH . 'wp-content/plugins/mentorCore/';

require_once ($plugin_dir .'inc/functions.php');
foreach (glob($plugin_dir ."templates/*.php") as $filename)
{
    include $filename;
}


class MentorCore
{

    function __construct()
    {
        add_action('init', array($this, 'custom_roles'));
        add_filter('show_admin_bar', array($this, 'hide_admin_bar'));
        add_filter('user_contractmethods', array($this, 'additional_user_fields'), 10, 1);
        add_action('wp_enqueue_scripts', array($this, 'my_load_scripts'));
        add_action('init', array($this, 'table_create'));

        // Hangle user login
        add_filter('wp_authenticate_user', array($this, 'check_status_on_login'), 10, 2);

    }


    function activate()
    {
        // lets find all mentees and mentors if there are any already!
        // get all mentee role users and put to mentee_list table

        preg_match('/^(.+)wp-content\/.*/', dirname(__FILE__), $path);
        include($path[1] . 'wp-load.php');

        $users = get_users();
        foreach ($users as $user) {
            if (get_user_meta($user->id, 'mentor_id', true) == '')
                update_user_meta($user->id, 'mentor_id', '-1');
            if (get_user_meta($user->id, 'organization', true) == '')
                update_user_meta($user->id, 'organization', 'Organization');
            if (get_user_meta($user->id, 'line_of_work', true) == '')
                update_user_meta($user->id, 'line_of_work', 'Other');
            if (get_user_meta($user->id, 'country', true) == '')
                update_user_meta($user->id, 'country', 'Germany');
            if (get_user_meta($user->id, 'qualification', true) == '')
                update_user_meta($user->id, 'qualification', 'Other');
            if (get_user_meta($user->id, 'account_activated', true) == '')
                update_user_meta($user->id, 'account_activated', 1);
            if (get_user_meta($user->id, 'activation_code', true) == '')
                update_user_meta($user->id, 'activation_code', '');

        }

        $this->create_pages();



    }

    function deactivate()
    {
        // flush rewrite rules
        // remove roles
        remove_role('mentor');
        remove_role('student');
        remove_filter('login_redirect', array($this, 'my_login_redirect'), 10);
    }

    function uninstall()
    {
        // delete CPT
        // delete all plugin data from database 
    }

    // set roles and polylang support
    function custom_roles()
    {
        add_role('mentor', 'Mentor', array(
            'edit_posts' => false,
            'delete_posts' => false
        ));
        add_role('student', 'Student', array(
            'edit_posts' => false,
            'delete_posts' => false
        ));

        pll_register_string('mentorCore', 'Hello student!');
        pll_register_string('mentorCore', 'Hello mentor!');
        pll_register_string('mentorCoreUser', 'About');
    }

    function my_login_redirect($url, $request, $user)
    {
        if ($user && is_object($user) && is_a($user, 'WP_User')) {
            if ($user->has_cap('administrator') or $user->has_cap('author')) {
                $url = admin_url();
            } else {
                $url = home_url() . 'My_profile';
            }
        }
        return $url;
    }

    function hide_admin_bar($show)
    {

        if (current_user_can('mentor') || current_user_can('student')) :
            return false;
        endif;

        return $show;
    }

    function my_load_scripts($hook)
    {

        // create my own version codes
        $my_js_ver = date("ymd-Gis", filemtime(plugin_dir_path(__FILE__) . 'js/scripts.js'));
        $my_css_ver = date("ymd-Gis", filemtime(plugin_dir_path(__FILE__) . 'style.css'));

        // 
        wp_deregister_script('jquery');
        wp_enqueue_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js', array(), null, true);
        wp_enqueue_script('custom_js', plugins_url('js/scripts.js', __FILE__), array(), $my_js_ver);
        wp_localize_script('custom_js', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
        wp_register_style('my_css', plugins_url('style.css', __FILE__), false, $my_css_ver);
        wp_enqueue_style('my_css');


        wp_register_script('chat-ajax-script', plugins_url( '/js/ajax-chat-script.js', __FILE__ ), array('jquery') );
        wp_localize_script( 'chat-ajax-script', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' )));

        wp_register_script('assessment-ajax-script', plugins_url( '/js/ajax-assesment-script.js', __FILE__ ), array('jquery') );

        wp_enqueue_script('MC-modal-script', plugins_url( '/js/modal.js', __FILE__ ), array('jquery') );



    }

    function table_create()
    {
        //if( ! headers_sent()) return;

        global $wpdb;
        //$table_name = $wpdb->prefix. "mentor_list";
        global $charset_collate;
        //$charset_collate = $wpdb->get_charset_collate();
        global $db_version;
        require_once(ABSPATH . "wp-admin/includes/upgrade.php");


        // mentee MUST be unique
        $table_name = $wpdb->prefix . "request_list";
        $charset_collate = $wpdb->get_charset_collate();
        if ($wpdb->get_var("SHOW TABLES LIKE '" . $table_name . "'") != $table_name) {
            $create_sql = "CREATE TABLE " . $table_name . " (
                id INT(11) NOT NULL auto_increment,
                request_user_id INT(11) NOT NULL UNIQUE,
                asked_user_id INT(11) NOT NULL,
                PRIMARY KEY (id))$charset_collate;";
            dbDelta($create_sql);
        }


        //register the new table with the wpdb object
        if (!isset($wpdb->ratings_fansub)) {
            $wpdb->ratings_fansub = $table_name;
            //add the shortcut so you can use $wpdb->stats
            $wpdb->tables[] = str_replace($wpdb->prefix, '', $table_name);
        }


        $table_name = $wpdb->prefix . "mentorCore_chat_room";
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            $charset_collate = $wpdb->get_charset_collate();
            $create_sql = "CREATE TABLE " . $table_name . " (
                id INT(11) NOT NULL auto_increment,
                student_id INT(11) NOT NULL,
                mentor_id INT(11) NOT NULL,
                PRIMARY KEY (id))$charset_collate;";
            dbDelta($create_sql);
        }

        //register the new table with the wpdb object
        if (!isset($wpdb->ratings_fansub)) {
            $wpdb->ratings_fansub = $table_name;
            //add the shortcut so you can use $wpdb->stats
            $wpdb->tables[] = str_replace($wpdb->prefix, '', $table_name);
        }

        $table_name = $wpdb->prefix . "mentorCore_chat_messages";
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            $charset_collate = $wpdb->get_charset_collate();
            $create_sql = "CREATE TABLE " . $table_name . " (
                id INT(11) NOT NULL auto_increment,
                room_id INT(11) NOT NULL,
                sender_id INT(11) NOT NULL,
                message TEXT NOT NULL, 
                PRIMARY KEY (id))$charset_collate;";
            dbDelta($create_sql);
        }
        //register the new table with the wpdb object
        if (!isset($wpdb->ratings_fansub)) {
            $wpdb->ratings_fansub = $table_name;
            //add the shortcut so you can use $wpdb->stats
            $wpdb->tables[] = str_replace($wpdb->prefix, '', $table_name);
        }

    }

    function check_status_on_login($user, $password){
        if( ! $user instanceof WP_User ) {
            return $user;
        }

        if(get_user_meta($user->ID, 'account_activated', true) == 0){
            $message = sprintf('%s', __('Your account hasn\'t been authenticated. Check your email', 'MClogin'));
            return new WP_Error( 'account_unauthenticated', $message );
        }

        if(get_user_meta($user->ID, 'account_activated', true) == -1){
            $message = sprintf('%s', __('Your account has been blocked. Contact support', 'MClogin'));
            return new WP_Error( 'account_blocked', $message );
        }

        return $user;
    }

    function create_pages()
    {
        $langs = pll_languages_list();
        $pages = array('chat', 'user_settings', 'user_profile');
        foreach ($pages as $page) {
            $arr = array();
            $content = '['.$page.']';
            foreach ($langs as $lang) {

                if( ($wppage = get_page_by_title( $page.'-'.$lang )) == NULL ){

                    $createPage = array(
                        'post_title'    => $page.'-'.$lang,
                        'post_content'  => $content,
                        'post_status'   => 'publish',
                        'post_author'   => 1,
                        'post_type'     => 'page',
                        'post_name'     => $page.'-'.$lang
                    );
                    $pageid = wp_insert_post( $createPage );
                    pll_set_post_language($pageid, $lang);
                    $arr[$lang] = $pageid;
                }
                else{
                    $arr[$lang] = $wppage->ID;
                }
            }
            pll_save_post_translations( $arr );
        }
    }

function create_page($title, $content){
    if( get_page_by_title( $title ) == NULL ){
        $createPage = array(
            'post_title'    => $title,
            'post_content'  => $content,
            'post_status'   => 'publish',
            'post_author'   => 1,
            'post_type'     => 'page',
            'post_name'     => $title
        );
        wp_insert_post( $createPage );
    }
}

}


if( class_exists('MentorCore') ){
    $mentorCore = new MentorCore();
}

// Activation
register_activation_hook( __FILE__, array($mentorCore, 'activate') );

// Deactivation
register_deactivation_hook( __FILE__, array($mentorCore, 'deactivate') );

// for creating posts/pages







add_action('user_register','registrationEdit');

function registrationEdit($user_id){

    //Set default mentor value
    update_user_meta($user_id, 'mentor_id', '-1');

    //Organization
    if ( isset( $_POST['organization'] ) )
        add_user_meta($user_id, 'organization', $_POST['organization']);
    else
        add_user_meta($user_id, 'organization', 'Organization');

    //Line of work
    if ( isset( $_POST['line_of_work'] ) )
        add_user_meta($user_id, 'line_of_work', $_POST['line_of_work']);
    else
        add_user_meta($user_id, 'line_of_work', 'Other');

    //Country
    if ( isset( $_POST['country'] ) )
        add_user_meta($user_id, 'country', $_POST['country']);
    else
        add_user_meta($user_id, 'country', 'Germany');

    //Qualification - position
    if ( isset( $_POST['qualification'] ) )
        add_user_meta($user_id, 'qualification', $_POST['qualification']);
    else
        add_user_meta($user_id, 'qualification', 'Other');

    //Confirmation
    $user_info = get_userdata($user_id);
    $code = md5(time());
    $string = array('id' =>$user_id, 'code'=>$code);
    update_user_meta($user_id, 'account_activated', 1); //TODO Change to 0
    //update_user_meta($user_id, 'activation_code', $code); //TODO Make activation
    update_user_meta($user_id, 'activation_code', '');
    //URL
    //$url = get_site_url(). '/account-activation/?act=' .base64_encode( serialize($string));
    //TODO Send activation email
    //$html = '<h1>Thank you for registering</h1>Please click the following links to activate your account <br/><br/> <a href="'.$url.'">'.$url.'</a>';
    //$headers = array('Content-Type: text/html; charset=UTF-8');
    //wp_mail( $user_info->user_email, __('CCJ4CJ website activation','text-domain') , $html, $header);

}

function myplugin_check_fields( $errors, $sanitized_user_login, $user_email ) {

    if ( ! preg_match('/[0-9]{5}/', $_POST['qualification'] ) ) {
        $errors->add( 'zipcode_error', __( '<strong>ERROR</strong>: Invalid Zip.', 'my_textdomain' ) );
    }

    return $errors;
}

add_filter( 'registration_errors', 'myplugin_check_fields', 10, 3 );



