<?php


class chat
{
    function __construct()
    {

    }



    public function can_room_exist($student, $mentor)
    {
        $mentorID = get_user_meta($student, 'mentor_id', true);
        if ($mentorID != -1 && $mentorID == $mentor)
            return true;
        return false;
    }

    public function does_room_exist($student, $mentor)
    {
        global $wpdb;
        $table = $wpdb->prefix . "mentorCore_chat_room";
        $chat = $wpdb->get_results("SELECT id FROM $table WHERE `student_id` = $student AND mentor_id = $mentor");
        if ($chat != null)
            return true;
        return false;
    }

    public function create_room($student, $mentor)
    {
        global $wpdb;
        $table = $wpdb->prefix . "mentorCore_chat_room";
        $wpdb->insert($table, array(
            "student_id" => $student,
            "mentor_id" => $mentor,
        ), array('%d', '%d'));
    }

    public function get_room_id($student, $mentor)
    {
        global $wpdb;
        $table = $wpdb->prefix . "mentorCore_chat_room";
        return $wpdb->get_results("SELECT id FROM $table WHERE `student_id` = $student AND mentor_id = $mentor")[0]->id;

    }


    public function canChatBeViewed($id1, $id2)
    {
        if (get_current_user_id() == $id1 || get_current_user_id() == $id2)
            return true;
        return false;
    }

    public function chat_message_count($chatID)
    {
        global $wpdb;
        $table = $wpdb->prefix . "mentorCore_chat_messages";
        $count = $wpdb->get_results($wpdb->prepare("SELECT COUNT(*) id FROM $table WHERE room_id = %d", $chatID));
        return $count[0]->id;
    }

    function get_chat_history($chatID, $count, $sender, $receiver)
    {
        global $wpdb;
        $table = $wpdb->prefix . "mentorCore_chat_messages";
        $messages = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE room_id = %d ORDER BY id DESC LIMIT %d", $chatID, $count));
        $messages = array_reverse($messages);
        $html = "";
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
        echo $html;
    }


}