
jQuery(document).ready(function($) {
    //sends message to chatbox
    jQuery('.send_chat_btn').click(function(){
        console.log("Send button pressed");
        let lastRecieved = $("#messages li").last().attr('data-messageID');
        let text = document.getElementById("chat_message_box").value;
        if(trimfield(text) != ''){
            let data = {
                'action': 'MentoringCore_chat',
                'sent_message':text,
                'student' : new URLSearchParams(window.location.search).get('student'),
                'mentor' : new URLSearchParams(window.location.search).get('mentor'),
                'msgid' : lastRecieved,
            };
            $.post('/wp-admin/admin-ajax.php', data, function(response) {
                document.getElementById("chat_message_box").value = '';
                $("#messages").append(response);
                updateScroll();
            });
        }
    });

    //On button click shows <= 10 older messages
    jQuery('#OlderMsgButton').click(function(){
        let lastRecieved = $("#messages li").first().attr('data-messageID');
        let data = {
            'action': 'Chat_GetOlderMessages',
            'msgid' : lastRecieved,
            'student' : new URLSearchParams(window.location.search).get('student'),
            'mentor' : new URLSearchParams(window.location.search).get('mentor'),
        };
        jQuery.post('/wp-admin/admin-ajax.php', data, function(response) {
            var $current_top_element = $('#messages :nth-child(2)');
            var previous_height = 0;
            $current_top_element.prevAll().each(function() {
                previous_height += $(this).outerHeight();
            });
            $(".getOlderDiv").after(response);
            $('#messages').scrollTop(previous_height);
        });
    });

    //Refreshes chatbox every 5 seconds
    setInterval(function(){
        let lastRecieved = $("#messages li").last().attr('data-messageID');
        let data = {
            'action': 'Update_chat',
            'msgid' : lastRecieved,
            'student' : new URLSearchParams(window.location.search).get('student'),
            'mentor' : new URLSearchParams(window.location.search).get('mentor'),
        };
        jQuery.post('/wp-admin/admin-ajax.php', data, function(response) {
            $("#messages").append(response);
        });
    },5000);
});