<?php
class Student{

    function __construct(){


    }
    public function profile(){
        if( ! headers_sent()) return; // waiting for headers to be sent
        $user = wp_get_current_user();
        $plugin_img_dir = WP_PLUGIN_URL ."/mentorCore/img/";
        global $wpdb;
       ?>
        <div class="inside_page">
            <div class="inside_page_left"></div>
            <div class="inside_page_middle">
                <p style="font-size:40px; font-weight:bold;"><?php pll_e("Hi,"); echo " ". $user->user_firstname  . " " .  $user->user_lastname . "!";?></p>
                <p style="font-size:18px;"><?php pll_e("Welcome back to your profile"); ?></p>
            </div>
            <div class="inside_page_right"></div>
        </div>

     <div class="section profile_pic">
            <div class="img_border">
                <a id="profile_picture" href="javascript:void(0)" onclick="change_profile_picture(<?php echo $user->ID;?>);"><?php print_r(get_avatar($user->ID));?></a>
                <form enctype="multipart/form-data" style="display:none;"><input accept="image/*" id="file-input" type="file" onchange="file_change(<?php echo $user->ID; ?>);" style="display:none;"/></form>
            </div>
        </div>


        <div class="section">
        <div class="section mentee">
            <div class="section-col">
                <div>
                    <p style="font-size:24px; color:#4b8d08; padding-bottom:40px; text-align:center;"><?php pll_e("About");?></p>

                    <p><a class="link" href="mailto:<?php echo $user->user_email;?>"><?php echo $user->user_email;?></a></p>
                    <p><?php echo get_user_meta($user->ID, 'country')[0]; ?></p>
                    <p><?php echo get_user_meta($user->ID, 'organization')[0];?></p>
                    <?php
                    /*//TODO SETTINGS
                    <a class="link" style="font-weight: normal;" href="/user_settings-<?php echo pll_current_language(); ?>">Edit settings</a>
                    */
                    ?>
                </div>
            </div>
            <div class="section-col nd">
                <div class="progress-container">
                    <p style="font-size:24px; color:#4b8d08; padding-bottom:40px;"><?php pll_e("Progress report");?></p>
                    <p><img src="<?php echo $plugin_img_dir;?>self-assessment_icon.png" alt="assesment icon"></p>
                    <p style="font-size:40px; color:#4b8d08;"><?php echo "85 %"?></p>
                    <p><a href="#" download><?php pll_e("Download a document");?></a></p>
                </div>
            </div>
            <div class="section-col">
                <?php
                

                $my_mentor = get_user_meta(get_current_user_id(), 'mentor_id', true);

                if($my_mentor > -1){// got mentor we shall take first array elem
                    $mento_info = get_user_by( "ID", $my_mentor);
                    // mentor info
                    ?>
                    <div class="mentee-container">
                        <p style="font-size:24px; color:#4b8d08; padding-bottom:40px;"><?php pll_e("My mentor");?></p>
                        <div class="mentee-thumb-row" style="display:flex;">
                            <div class="mentee-thumb-col">
                                <?php print_r(get_avatar($my_mentor));?>
                            </div>
                            <div class="mentee-thumb-col">
                                <p style="font-weight:bold; font-size:16px; color:#333333;"><?php echo $mento_info->user_firstname . " " . $mento_info->user_lastname;?></p>
                                <p><?php echo get_user_meta($user->ID, 'qualification')[0];?></p>
                            </div>
                        </div>
                        <div class="mentee-thumb-inner social">
                            <div>
                                <img src="<?php echo $plugin_img_dir;?>diskusijo_ikonele.png" alt="chat">
                                <a href="/chat?student=<?php echo $user->ID ?>&mentor=<? echo $mento_info->ID ?>"><?php pll_e("Chat");?></a>
                            </div>
                            <div>
                                <img src="<?php echo $plugin_img_dir;?>remove_ikonele.png" alt="trash-can">
                                <a href="javascript:void(0)" onclick="unbind_mentor('<?php echo $user->ID;?>')" ><?php pll_e("Remove");?></a>
                            </div>
                        </div>
                    </div>
                    <?php
                }else{
                    ?>
                    <div class="green-btn"><a href="javascript:void(0)" onclick="i_want_mentor();"><div><?php pll_e("I want a mentor");?></div></a></div>
                    <?php
                }
                ?>
                 <div class="green-btn hidden_btn"><a href="javascript:void(0)" onclick="i_want_mentor();"><div><?php pll_e("I want a mentor");?></div></a></div>
            </div>
        </div>
        </div>
        <div class="section available_mentors">
            <div class="section-banner"><div style="position:absolute;"><p><?php pll_e("Available mentors");?></p></div></div>   
            <div class="mentor_window">
                <div style="display:flex; justify-content:end;"><a style="margin-right:10px;" href="javascript:void(0)" onclick="close_mentor_selection();"><img src="<?php echo $plugin_img_dir;?>exit.png"></a></div>
                <div class="mentor_list">
                    <?php
                        // get list of requests for this user
                        $request_list = array();
                        
                        $table_name = $wpdb->prefix. "request_list";
                        $sql = "SELECT * 
                                FROM ".$table_name . " 
                                WHERE request_user_id ='".$user->ID."'";
                        $request_list = $wpdb->get_results($sql);                              



                        // get list of available mentors
                        $mentor_list = get_users(array(
                            'role'       => 'mentor',
                            'meta_key'   =>  'account_activated',
                            'meta_value' => '1',
                        ));

                        if(count($mentor_list) < 1){
                            ?>
                            <div><p> <?php pll_e("Currently there are no available mentors"); ?></p></div>
                            <?php
                        }
                        //$mentor_list = array(1, 5, 6, 8, 9); // for testing
                        foreach($mentor_list as $mentor){
                            ?>
                            <div id="<?php echo $mentor->ID;?>" class="mentor_info">
                                <?php print_r(get_avatar($mentor->ID));?>
                                <div class="mentor_data">
                                    <p style="font-weight:bold; font-size:16px; color:#333333;"><?php echo $mentor->user_firstname . " " . $mentor->user_lastname ?></p>
                                    <p style="font-size:16px; margin: 10px 0;"><?php echo get_user_meta($mentor->ID, 'qualification')[0];?></p>
                                <?php if(count($request_list) > 0){
                                    if ( $request_list[0]->asked_user_id == $mentor->ID){ //hide only request buttons ?>
                                        
                                        <a class="request-btn ask hide" href="javascript:void(0)" onclick="send_friend_request(<?php echo $user->ID; ?>,<?php echo $mentor->ID;?>);"><div><?php pll_e("Request");?></div></a>
                                        <a class="request-btn cancel show" href="javascript:void(0)" onclick="cancel_friend_request(<?php echo $user->ID; ?>,<?php echo $mentor->ID;?>);"><div><?php pll_e("Cancel");?></div></a>
                                        <?php }else{ //both hide ?>
                                        <a class="request-btn ask hide" href="javascript:void(0)" onclick="send_friend_request(<?php echo $user->ID; ?>,<?php echo $mentor->ID;?>);"><div><?php pll_e("Request");?></div></a>
                                        <a class="request-btn cancel hide" href="javascript:void(0)" onclick="cancel_friend_request(<?php echo $user->ID; ?>,<?php echo $mentor->ID;?>);"><div><?php pll_e("Cancel");?></div></a>
                                    <?php }
                                }else{ // default hide cancel button
                                    ?>
                                    <a class="request-btn ask" href="javascript:void(0)" onclick="send_friend_request(<?php echo $user->ID; ?>,<?php echo $mentor->ID;?>);"><div><?php pll_e("Request");?></div></a>
                                    <a class="request-btn cancel hide" href="javascript:void(0)" onclick="cancel_friend_request(<?php echo $user->ID; ?>,<?php echo $mentor->ID;?>);"><div><?php pll_e("Cancel");?></div></a>
                                   
                                    <?php } ?>
      
                                </div>
                            </div>
                            <?php
                        }
                    ?>
                </div>
            </div>    
        </div>
        <?php
    }
}