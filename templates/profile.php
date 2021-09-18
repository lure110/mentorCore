<?php

preg_match('/^(.+)wp-content\/.*/', dirname(__FILE__), $path);
include($path[1] . 'wp-load.php');

require_once ($plugin_dir .'inc/mentor.class.php');
require_once ($plugin_dir .'inc/student.class.php');


function profile_display(){
    if(is_user_logged_in(  )){
        $user = wp_get_current_user();
        if ( in_array( 'student', $user->roles ) ) {
            $obj = new Student();
        }else if ( in_array( 'mentor', $user->roles ) || in_array( 'administrator', $user->roles ) ) {
            $obj = new Mentor();
        }
        if(isset($obj)){
            $obj->profile();
        }
    }else{
        ?>
            <div>
                <p><?php pll_e("First you must log in to see your profile");?></p>
            </div>
        <?php
    }

}
add_shortcode( 'user_profile', 'profile_display');
