<?php

defined( 'ABSPATH' ) or die( );
//preg_match('/^(.+)wp-content\/.*/', dirname(__FILE__), $path);
//include($path[1] . 'wp-load.php');

require_once ($plugin_dir .'inc/chat.class.php');


function chat_display(){
    ob_start();
    $studentID = $_GET["student"];
    $mentorID = $_GET["mentor"];
    wp_enqueue_script( 'chat-ajax-script');
    do_action('Chat_Javascript');
    $chat = new chat();
            if (!$chat->does_room_exist($studentID, $mentorID))
                $chat->create_room($studentID, $mentorID);

            $chatID = $chat->get_room_id($studentID, $mentorID);
            $messageCount = $chat->chat_message_count($chatID);

            if(get_current_user_id() == $studentID){
                $sender = $studentID;
                $receiver = $mentorID;}
            else{
                $sender = $mentorID;
                $receiver = $studentID;}

            ?>
            <div class="ChatBox">
                <p style="text-align: center; font-size: 18px; font-weight: bold; color: #333333;">Chat with <?php echo get_user_meta($receiver, 'first_name', true).' '.get_user_meta($receiver, 'last_name', true); ?></p>
                <div class="MessageBox">
                    <ul id="messages">
                        <?php if($messageCount > 20){
                            ?>
                            <div class="getOlderDiv"><a name="GetOlderMessages" id="OlderMsgButton" class="get_oldermsgs_btn"><?php pll_e('Show older'); ?></a></div>
                            <?php
                            $chat->get_chat_history($chatID, 20, $sender, $receiver);
                        }
                        if($messageCount <= 20)
                            $chat->get_chat_history($chatID, $messageCount, $sender, $receiver);
                        ?>
                    </ul>
                </div>
                <div class="sendMessage">
                    <textarea name="chat_message" id="chat_message_box" class="send_message_box form-control" placeholder="<?php pll_e('Type your message...') ?>"></textarea>
                    <button type="button" name="send_chat" class="btn btn-info send_chat_btn"><?php pll_e('Send'); ?></button>
                </div>
            </div>

            <?php
    return ob_get_clean();
}
add_shortcode( 'chat', 'chat_display');

function pre_process_shortcode()
{
    if (!is_singular()) return;
    global $post;
    if (!empty($post->post_content)) {
        if (has_shortcode($post->post_content, 'chat')) {
            if (isset($_GET["student"]) && isset($_GET["mentor"])) {
                $studentID = $_GET["student"];
                $mentorID = $_GET["mentor"];
                $chat = new chat();
                if (!$chat->canChatBeViewed($studentID, $mentorID) && !$chat->can_room_exist($studentID, $mentorID)) {
                    if (!is_admin()) {
                        wp_redirect(home_url());
                        exit();
                    }
                }
            }
            else{
                if (!is_admin()) {
                    wp_redirect(home_url());
                    exit();
                }
            }
        }

    }
}
add_action('template_redirect','pre_process_shortcode',1);
