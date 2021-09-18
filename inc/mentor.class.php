<?php
class Mentor{
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



        <div class="section mentor" style="margin-bottom:25px;">
            <div class="section-col about">
                <div>
                    <p style="font-size:24px; color:#4b8d08; padding-bottom:40px;"><?php pll_e("About");?></p>
                    <p><a style="color:#4b8d08;" href="mailto:<?php echo $user->user_email;?>"><?php echo $user->user_email;?></a></p>
                    <p><?php echo get_user_meta($user->ID, 'country')[0];?></p>
                    <p><?php echo get_user_meta($user->ID, 'organization')[0];?></p>
                    <?php
                    /*//TODO SETTINGS
                    <a class="link" style="font-weight: normal;" href="/user_settings-<?php echo pll_current_language(); ?>">Edit settings</a>
                    */
                    ?>
                </div>
            </div>
            <div class="section-col request">
                
                <p style="font-size:24px; color:#4b8d08; padding-bottom:40px;" ><?php pll_e("Mentoring request");?></p>
                <?php
                // we shall take all requests from request_list for asked_user_id is our user ID
                $friend_requests = array();
                $table_name = $wpdb->prefix. "request_list";
                $sql = "SELECT * 
                        FROM ".$table_name." 
                         WHERE asked_user_id='".$user->ID."'";
                $friend_requests = $wpdb->get_results($sql);
                foreach($friend_requests as $request){
                    $request_user = get_user_by( "ID", $request->request_user_id);
                    ?>
                    <div id="<?php echo $request->request_user_id; ?>" class="mentoring_request">
                        <p><?php echo $request_user->user_firstname . " " . $request_user->user_lastname; ?></p>
                        <a href="javascript:void(0)" onclick="accept_mentor_request(<?php echo $request->request_user_id; ?>, <?php echo $user->ID; ?> );"><?php pll_e("Submit");?></a>
                    </div><?php
                }
                ?>
            </div>
        </div>

        <div class="section">
            <div class="section-banner"><div style="position:absolute;"><p><?php pll_e("My mentees");?></p></div></div>
            <div class="section-container">
              
                
                <?php
                    // take mentees from db mentee_list where current_mentor_id is our user id
                    $mentee_array = array();
                    $mentee_array = get_users(array(
                        'meta_key' => 'mentor_id',
                        'meta_value' => $user->ID,
                    ));
                ?>
                <div class="mentee-thumb">
                    <?php 
                    if( count($mentee_array) < 1){
                        ?>
                        <div class="default_text"><?php pll_e("You have no mentees"); ?></div>
                        <?php
                    }else{
                    foreach ($mentee_array as $mentee) { 
                        $mentee_user = get_user_by( "ID", $mentee->ID);
                        ?>
                    <div id="<?php echo $mentee->ID; ?>" class="mentee-thumb-row">
                        <div class="mentee-thumb-inner">
                            <div class="mentee-thumb-col">
                                <?php print_r(get_avatar($mentee->ID));?>
                            </div>
                            <div class="mentee-thumb-col">
                                <p style="font-weight:bold;"><?php echo $mentee_user->user_firstname . " " . $mentee_user->user_lastname;?></p>
                                <p><a class="link" href="mailto:<?php echo $mentee_user->user_email; ?>"><?php echo $mentee_user->user_email; ?></a></p>
                                <p><?php echo get_user_meta($mentee->ID, 'country', true); ?></p>
                                <p><?php echo get_user_meta($mentee->ID, 'organization', true);?></p>
                            </div>
                        </div>
                        <div class="mentee-thumb-inner social">
                            <div>
                                <img style="padding-bottom:4px;" src="<?php echo $plugin_img_dir;?>diskusijo_ikonele.png" alt="chat">
                                <a href="/chat?student=<?php echo $mentee->ID ?>&mentor=<? echo $user->ID ?>"><?php pll_e("Chat");?></a>
                            </div>
                            <div>
                                <img style="padding-left:10px;" src="<?php echo $plugin_img_dir;?>progreso_ikonele.png" alt="progress">
                                <a href="#"><?php pll_e("Progress");?></a>
                            </div>
                            <div>
                                <img style="padding-bottom:12px; padding-left:24px;" src="<?php echo $plugin_img_dir;?>remove_ikonele.png" alt="trash-can">
                                <a href="javascript:void(0)" onclick="remove_user('<?php echo $mentee->ID;?>')"><?php pll_e("Remove");?></a>
                            </div>
                        </div>
                    </div>
                    <?php }} ?>
                </div>
            </div>
        </div>

        <?php
    }
}