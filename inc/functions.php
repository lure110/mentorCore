<?php

function be_custom_avatar_field( $user ) { ?>
	<h3>Custom Avatar</h3>
	 
	<table>
	<tr>
	<th><label for="custom_avatar">Custom Avatar URL:</label></th>
	<td>
	<input type="text" name="custom_avatar" id="custom_avatar" value="<?php echo esc_url_raw( get_the_author_meta( 'custom_avatar', $user->ID ) ); ?>" /><br />
	<span>Type in the URL of the image you'd like to use as your avatar. This will override your default Gravatar, or show up if you don't have a Gravatar. <br /><strong>Image should be 70x70 pixels.</strong></span>
	</td>
	</tr>
	</table>
	<?php 
}



add_action( 'show_user_profile', 'be_custom_avatar_field' );
add_action( 'edit_user_profile', 'be_custom_avatar_field' );


function be_save_custom_avatar_field( $user_id ) {
	if ( current_user_can( 'edit_user', $user_id ) ) {
        update_user_meta( $user_id, 'custom_avatar', esc_url_raw( $_POST['custom_avatar'] ) );
	}
}
add_action( 'personal_options_update', 'be_save_custom_avatar_field' );
add_action( 'edit_user_profile_update', 'be_save_custom_avatar_field' );

function be_gravatar_filter($avatar, $id_or_email, $size, $default, $alt) {
    $plugin_img_dir = WP_PLUGIN_URL ."/mentorCore/img/";
	// If provided an email and it doesn't exist as WP user, return avatar since there can't be a custom avatar
	$email = is_object( $id_or_email ) ? $id_or_email->comment_author_email : $id_or_email;
	if( is_email( $email ) && ! email_exists( $email ) )
		return $avatar;
	
	$custom_avatar = get_user_meta($id_or_email,'custom_avatar');

	if ($custom_avatar) 
		$return = '<img src="'.$custom_avatar[0].'" width="'.$size.'" height="'.$size.'" alt="'.$alt.'" />';
	else 
		$return = '<img src="'.$plugin_img_dir ."dummypic.png".'" width="'.$size.'" height="'.$size.'" alt="'.$alt.'" />';

	return $return;
}
add_filter('get_avatar', 'be_gravatar_filter', 10, 5);

/* AJAX CALLS */

add_action( 'wp_ajax_upload_my_new_profile_pic', 'upload_my_new_profile_pic');

function upload_my_new_profile_pic(){
    if ( ! function_exists( 'wp_handle_upload' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
    }

    $user_id = $_POST['id'];
    
    $uploadedfile = $_FILES['file'];
    $upload_overrides = array( 'test_form' => false );

    $movefile = wp_handle_upload( $uploadedfile, $upload_overrides ); 

    if ( $movefile && !isset( $movefile['error'] ) ) {
        if(! function_exists('update_user_meta')){require_once( ABSPATH . 'wp-admin/includes/user.php' ); }
        $user_pic_meta = get_user_meta( $user_id, 'custom_avatar', true );
        if( ! $user_pic_meta){
            add_user_meta($user_id, 'custom_avatar', $movefile['url']);
        }else{ // remove old and add new
            //wp_delete_file(get_user_meta($user_id, 'custom_avatar')[0]); must provide phys address
            update_user_meta( $user_id, 'custom_avatar', $movefile['url'] );
        }
        echo get_user_meta($user_id, 'custom_avatar')[0];
    } else {
        /**
         * Error generated by _wp_handle_upload()
         * @see _wp_handle_upload() in wp-admin/includes/file.php
         */
        print_r($movefile);
    }

    wp_die();
}



add_action( 'wp_ajax_user_remove', 'user_remove');

function user_remove(){
    $user_id = $_POST['id'];

    update_user_meta($user_id, 'mentor_id', -1);

    global $wpdb;
    $table_name = $wpdb->prefix. "mentee_list";
    $sql = "UPDATE ". $table_name . "
    SET current_mentor_id='-1'
    WHERE mentee_id='".$user_id."'";
    $results = $wpdb->query($sql);
    wp_die();
}
add_action( 'wp_ajax_send_friend_request', 'send_friend_request');

function send_friend_request(){
    $sender = $_POST['sender'];
    $receiver = $_POST['receiver'];
    
    global $wpdb;
    $table_name = $wpdb->prefix. "request_list";
    $sql = "INSERT INTO ". $table_name . "
    (request_user_id, asked_user_id)
    VALUES (".$sender.", ".$receiver.")";
    $results = $wpdb->query($sql);   
    wp_die();
}


add_action( 'wp_ajax_cancel_friend_request', 'cancel_friend_request');

function cancel_friend_request(){
    $sender = $_POST['sender'];
    $receiver = $_POST['receiver'];
    
    global $wpdb;
    $table_name = $wpdb->prefix. "request_list";
    $sql = "DELETE FROM ". $table_name . "
    WHERE request_user_id='".$sender."' AND asked_user_id='".$receiver."'";
    $results = $wpdb->query($sql);   
    wp_die();
}

add_action( 'wp_ajax_accept_friend_request', 'accept_friend_request');

function accept_friend_request(){
    $plugin_img_dir = WP_PLUGIN_URL ."/mentorCore/img/";
    $sender = $_POST['sender'];
    $receiver = $_POST['receiver'];

    update_user_meta($sender, 'mentor_id', $receiver);

    global $wpdb;
    $table_name = $wpdb->prefix. "mentee_list";
    $sql = "UPDATE ". $table_name . "
    SET current_mentor_id='".$receiver."'
    WHERE mentee_id='".$sender."'";
    $results = $wpdb->query($sql); 
    
    $table_name = $wpdb->prefix. "request_list";
    $sql = "DELETE FROM ". $table_name . "
    WHERE request_user_id='".$sender."' AND asked_user_id='".$receiver."'";
    $results = $wpdb->query($sql); 
        

        $mentee_user = get_user_by( "ID", $sender);
        ?>
    <div id="<?php echo $sender; ?>" class="mentee-thumb-row">
        <div class="mentee-thumb-inner">
            <div class="mentee-thumb-col">
                <?php print_r(get_avatar($mentee_user->ID)); ?>
            </div>
            <div class="mentee-thumb-col">
                <p style="font-weight:bold;"><?php echo $mentee_user->user_firstname . " " . $mentee_user->user_lastname;?></p>
                <p><a class="link" href="mailto:<?php echo $mentee_user->user_email; ?>"><?php echo $mentee_user->user_email; ?></a></p>
                <p><?php echo get_user_meta($mentee_user->ID, 'country', true); ?></p>
                <p><?php echo get_user_meta($mentee_user->ID, 'organization', true); ?></p>
            </div>
        </div>
        <div class="mentee-thumb-inner social">
            <div>
                <img style="padding-bottom:4px;" src="<?php echo $plugin_img_dir;?>diskusijo_ikonele.png" alt="chat">
                <a href="/chat?student=<?php echo $mentee_user->ID ?>&mentor=<? echo get_current_user_id() ?>"><?php pll_e("Chat");?></a>
            </div>
            <div>
                <img style="padding-left:10px;" src="<?php echo $plugin_img_dir;?>progreso_ikonele.png" alt="progress">
                <a href="#"><?php pll_e("Progress");?></a>
            </div>
            <div>
                <img style="padding-bottom:12px; padding-left:24px;" src="<?php echo $plugin_img_dir;?>remove_ikonele.png" alt="trash-can">
                <a href="javascript:void(0)" onclick="remove_user('<?php echo $sender;?>')"><?php pll_e("Remove");?></a>
            </div>
        </div>
    </div>
    <?php
    
    wp_die();
}

add_action( 'wp_ajax_try_to_login', 'try_to_login');
add_action( 'wp_ajax_nopriv_try_to_login', 'try_to_login');

function try_to_login()
{
    check_ajax_referer('ajax-login-nonce', 'MCsecurity', false);
    $nonce = isset($_REQUEST['MCsecurity']) ? sanitize_text_field(wp_unslash($_REQUEST['MCsecurity'])) : false;

    $flag = wp_verify_nonce($nonce, 'ajax-login-nonce');
    if (true != $flag || is_wp_error($flag)) {
        //TODO Change __('');
        wp_send_json_error(
            array(
                'message' => __('Nonce error, please reload.', 'user-registration'),
            )
        );
    }


    $creds = array(
        'user_login' => $_POST['username'],
        'user_password' => $_POST['password'],
        'remember' => true
    );
    $user = wp_signon($creds);

    if (is_wp_error($user)) {

        // set the custom error message
        if (!empty($user->errors['empty_username'])) {
            $user->errors['empty_username'][0] = sprintf('%s', __('Username field empty', 'MClogin'));
        }
        if (!empty($user->errors['empty_password'])) {
            $user->errors['empty_password'][0] = sprintf('%s', __('Password field empty', 'MClogin'));
        }
        if (!empty($user->errors['invalid_username'])) {
            $user->errors['invalid_username'][0] = sprintf('%s', __('Username or password is incorrect', 'MClogin'));
        }
        if (!empty($user->errors['incorrect_password'])) {
            $user->errors['incorrect_password'][0] = sprintf('%s', __('Username or password is incorrect', 'MClogin'));
        }
        if (!empty($user->errors['account_unauthenticated'])) {
            $user->errors['account_unauthenticated'][0] = sprintf('%s', __('Your account hasn\'t been authenticated. Check your email', 'MClogin'));
        }
        if (!empty($user->errors['account_blocked'])) {
            $user->errors['account_blocked'][0] = sprintf('%s', __('Your account has been blocked. Contact support', 'MClogin'));
        }
        $message = $user->get_error_message();
        wp_send_json_error(array('message' => $message));

    } else {
        if (in_array('administrator', $user->roles))
            $redirect = admin_url();
        else $redirect = home_url();
    }
    wp_send_json_success(array('message' => $redirect));
    wp_die();
}


add_action( 'wp_ajax_try_to_register', 'try_to_register');
add_action( 'wp_ajax_nopriv_try_to_register', 'try_to_register');

function try_to_register(){
    wp_die();
}

add_action( 'wp_ajax_register_validation', 'register_validation');
add_action( 'wp_ajax_nopriv_register_validation', 'register_validation');

function register_validation(){

    check_ajax_referer('ajax-register-nonce', 'MCsecurity', false);


}




/*-----------------------CHAT------------------------------*/

function insert_message_to_chat($chat_id, $sender, $message){
    global $wpdb;
    $table = $wpdb->prefix."mentorCore_chat_messages";
    $wpdb->insert($table,array(
        'room_id' => $chat_id,
        'sender_id' => $sender,
        'message' => $message
    ),array('%d','%d','%s'));
    return $wpdb->insert_id;
}


add_action( 'Chat_Javascript', 'js_chat' );
function js_chat(){
    ?><script type="text/javascript" >

        window.onload = updateScroll;
    </script> <?php
}

add_action('wp_ajax_Chat_GetOlderMessages', 'Chat_GetOlderMessages');
function Chat_GetOlderMessages(){
    global $wpdb;
    $oldest = $_POST['msgid'];
    $student = $_POST['student'];
    $mentor = $_POST['mentor'];
    if($student == get_current_user_id()){
        $sender = $student;
        $receiver = $mentor;
    }
    else{
        $sender = $mentor;
        $receiver = $student;
    }



    $table = $wpdb->prefix . "mentorCore_chat_room";
    $chatID = $wpdb->get_results("SELECT id FROM $table WHERE `student_id` = $student AND mentor_id = $mentor")[0]->id;
    $html = '';
    $table = $wpdb->prefix . "mentorCore_chat_messages";
    $messages = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE room_id = %d AND id < %d ORDER BY id DESC LIMIT 20",$chatID,$oldest));
    $messages = array_reverse($messages);
    foreach ($messages as $message) {
        if ($message->sender_id == $sender) {

            $html .= '<li class="sender" data-messageID="' . $message->id . '">
            <div class="messDiv">
			
                <div class="messContainer"><p class="mess">' . htmlentities($message->message, ENT_QUOTES) . '</p></div>
                ' . get_avatar($sender, 128, '', 'Avatar', array(
                    'class' => 'avatar avatar-128 wp-user-avatar wp-user-avatar-128 photo avatar-default'
                )) . '
            </div>
            </li>';
        } else if ($message->sender_id == $receiver) {
            $html .= '<li class="receiver" data-messageID="' . $message->id . '">
            <div class="messDiv">
			 ' . get_avatar($receiver, 128, '', 'Avatar', array(
                    'class' => 'avatar avatar-128 wp-user-avatar wp-user-avatar-128 photo avatar-default'
                )) . '		
                <div class="messContainer" ><p class="mess">' . htmlentities($message->message, ENT_QUOTES) . '</p></div>
            </div>
            </li>';
        }
    }
    if(count($messages) < 20)
    {
        ?>
        <script>
            var elem = document.getElementById('OlderMsgButton');
            elem.parentNode.removeChild(elem);
        </script>
        <?php
    }
    echo $html;
    wp_die();
}

add_action('wp_ajax_Update_chat', 'Update_chat');
function Update_chat(){
    global $wpdb;
    $lastMessageID = $_POST['msgid'];
    $student = $_POST['student'];
    $mentor = $_POST['mentor'];

    if(!isset($lastMessageID))
    $lastMessageID = 0;

    if($student == get_current_user_id()){
        $sender = $student;
        $receiver = $mentor;
    }
    else{
        $sender = $mentor;
        $receiver = $student;
    }
    $table = $wpdb->prefix . "mentorCore_chat_room";
    $chatID = $wpdb->get_results("SELECT id FROM $table WHERE `student_id` = $student AND mentor_id = $mentor")[0]->id;
    $html = getMessages($chatID, $receiver, $lastMessageID);
    echo $html;
    wp_die();
}

function getMessages($chatID, $sender, $last)
{
    global $wpdb;
    $table = $wpdb->prefix . "mentorCore_chat_messages";
    if($last == NULL)
        $last = 0;

    $messages = $wpdb->get_results("SELECT * FROM $table WHERE room_id = $chatID AND sender_id = $sender AND id > $last ");
    $html = '';

    foreach($messages as $message)
    {
        $html .= '<li class="receiver" data-messageID="' . $message->id . '">
            <div class="messDiv">
			 ' . get_avatar($sender, 128, '', 'Avatar', array(
                'class' => 'avatar avatar-128 wp-user-avatar wp-user-avatar-128 photo avatar-default'
            )) . '		
                <div class="messContainer" ><p class="mess">' . htmlentities($message->message, ENT_QUOTES) . '</p></div>
            </div>
            </li>';
        echo "<script>updateScroll();</script>";
    }
    return $html;
}

add_action('wp_ajax_MentoringCore_chat', 'MentoringCore_chat');
function MentoringCore_chat(){
    global $wpdb;
    $message = stripslashes($_POST['sent_message']);
    $student = $_POST['student'];
    $mentor = $_POST['mentor'];
    if($student == get_current_user_id()){
        $sender = $student;
        $receiver = $mentor;
    }
    else{
        $sender = $mentor;
        $receiver = $student;
    }
    $table = $wpdb->prefix . "mentorCore_chat_room";
    $chatID = $wpdb->get_results("SELECT id FROM $table WHERE `student_id` = $student AND mentor_id = $mentor")[0]->id;
    $last = $_POST['msgid'];
    $html = getMessages($chatID, $receiver, $last);
    $id = insert_message_to_chat($chatID, $sender, $message);
    $html .= '<li class="sender" data-messageID="' . $id . '">
            <div class="messDiv">
			
                <div class="messContainer"><p class="mess">' . htmlentities($message, ENT_QUOTES) . '</p></div>
                ' . get_avatar($sender, 128, '', 'Avatar', array(
            'class' => 'avatar avatar-128 wp-user-avatar wp-user-avatar-128 photo avatar-default'
        )) . '
            </div>
            </li>';
    echo $html;
    wp_die();
}


/*---------------------CHAT-END----------------------------*/