function remove_user(id){
    jQuery(document).ready(function(e){
        jQuery.ajax({
           type: 'POST',
           url: ajax_object.ajax_url,
           data: { 
                'action': 'user_remove', //calls wp_ajax_nopriv_ajaxremove
                'id': id
            },
           success: function(data){
               // refresh list

               jQuery(".mentee-thumb #" + id).remove();
           },
           error: function(data){
           }
        });
       return false;
    });
}

function cancel_friend_request(sender, receiver){


    jQuery(document).ready(function(e){
        jQuery.ajax({
           type: 'POST',
           url: ajax_object.ajax_url,
           data: { 
                'action': 'cancel_friend_request', //calls wp_ajax_nopriv_ajaxremove
                'sender': sender,
                'receiver':receiver
            },
           success: function(data){
               // refresh list
                // if success make request button and add other request buttons
                jQuery(".request-btn.cancel").hide();
                jQuery(".request-btn.ask").show();

           },
           error: function(data){
           }
        });
       return false;
    });
}


/* only mentee section */
function i_want_mentor(){
    jQuery(".section.mentee").hide('fast');
    jQuery(".section.available_mentors").show('fast');
}
function close_mentor_selection(){
    jQuery(".section.available_mentors").hide('fast');
    jQuery(".section.mentee").show('fast');
}

function send_friend_request(sender, receiver){
    jQuery(document).ready(function(e){
        jQuery.ajax({
           type: 'POST',
           url: ajax_object.ajax_url,
           data: { 
                'action': 'send_friend_request', //calls wp_ajax_nopriv_ajaxremove
                'sender': sender,
                'receiver':receiver
            },
           success: function(data){
               // refresh list
               jQuery(".request-btn.ask").hide('slow');
               jQuery(".mentor_list #" + receiver + " div .request-btn.cancel").show('slow');
           },
           error: function(data){

           }
        });
       return false;
    });
    // if success make cancel button and remove other request buttons

}

/* only mentor section */

function accept_mentor_request(sender, receiver){
    jQuery(document).ready(function(e){
        jQuery.ajax({
           type: 'POST',
           url: ajax_object.ajax_url,
           data: { 
                'action': 'accept_friend_request', //calls wp_ajax_nopriv_ajaxremove
                'sender': sender,
                'receiver':receiver
            },
           success: function(data){
               // refresh list
                // if success make cancel button and remove other request buttons
               jQuery(".mentoring_request#"+sender).hide('fast');
               jQuery(".default_text").hide('fast');
               jQuery(".mentee-thumb").append(data);
               // add to the block
           },
           error: function(data){
           }
        });
       return false;
    });

}

function change_profile_picture(id){
    jQuery("#file-input").trigger('click');
}

function file_change(id){
    let file_data = jQuery('#file-input').prop('files')[0];
    console.log(file_data);
    if(file_data === undefined) return;

    var form_data = new FormData();
    form_data.append('id', id);
    form_data.append('file', file_data);
    form_data.append('action','upload_my_new_profile_pic');
    
    jQuery.ajax({
        url: ajax_object.ajax_url,
        type: 'POST',
        contentType: false,
        processData: false,
        async: true,
        cache: false,
        enctype: 'multipart/form-data',
        data: form_data,
        success: function(response){
            if(response !== undefined || response != ''){
                jQuery('#profile_picture img').attr('src',response);
            }
        },
        error: function(response){
        }
    });
    return false;
}

function open_login_form(){
    jQuery('.pop_up').show('slow');
}
function close_login_popup(){
    jQuery('.pop_up').hide('slow');
    jQuery('#log_status').hide();
}

function try_log_in(){
    console.log("lets try to login me!");
    jQuery(document).ready(function(e){
        jQuery.ajax({
           type: 'POST',
           url: ajax_object.ajax_url,
           data: { 
                'action': 'try_to_login', //calls wp_ajax_nopriv_ajaxremove
                'username': document.getElementById('log_username').value,
                'password':document.getElementById('log_password').value,
                'MCsecurity':document.getElementById('loginSecurity').value
            },
           success: function(response) {
               //
               console.log("success");
               if (response.success === false) {
                   jQuery('#log_status').text(response.data.message);
                   jQuery('#log_status').show('slow');
               } else if (response.success === true) {
                   window.location.href = response.data.message;
               }

           }
        });
    });
    return false;
}

function try_to_register_mentor(){
    jQuery(document).ready(function(e){
        jQuery.ajax({
            type: 'POST',
            url: ajax_object.ajax_url,
            data: {
                'action': 'register_validation',
                'role' : 'mentor',
                'username' : document.getElementById('reg_username').value,
                'password' : document.getElementById('reg_password').value,
                'cpassword' : document.getElementById('reg_cpassword').value,
                'email' : document.getElementById('reg_email').value,
                'line_of_work' : document.getElementById('reg_qualification').value,
                'organization' : document.getElementById('reg_organization').value,
                'country' : document.getElementById('reg_country').value,
                'fname' : document.getElementById('reg_fname').value,
                'lname' : document.getElementById('reg_lanem').value,
                'MCsecurity':document.getElementById('registerSecurity').value
            }
        });
    });
}

function unbind_mentor(id){
    jQuery(document).ready(function(e){
        jQuery.ajax({
           type: 'POST',
           url: ajax_object.ajax_url,
           data: { 
                'action': 'user_remove', //calls wp_ajax_nopriv_ajaxremove
                'id': id
            },
           success: function(data){
               // refresh list
                jQuery('.section-col .mentee-container').hide('fast');
                jQuery('.green-btn.hidden_btn').show('fast');
           },
           error: function(data){
           }
        });
       return false;
    });
}

// neveikia
function try_register_student(){
    console.log("lets try to register me!");
    jQuery(document).ready(function(e){
        jQuery.ajax({
           type: 'POST',
           url: ajax_object.ajax_url,
           data: { 
                'action': 'try_to_register', //calls wp_ajax_nopriv_ajaxremove
                'name': a,
                'email':e,
                'country':c,
                'organizaion':o,
                'username': document.getElementById('log_username').value,
                'password':document.getElementById('log_password').value,
                'competencies':com,
                'carrer_development_before': cdb,
                'expectations': exp,
                'line_of_work': low,
                'position_in_career':pic
            },
           success: function(data){
                console.log(data);
               // window.location.href = data;
           },
           error: function(data){
               console.log(data);
           }
        });
    });
    return false;
}

function show_register_selection(){
    jQuery(".new_user_question").hide('fast');
    jQuery('#login_form').hide('fast');
    jQuery('.select_register_type').show('fast');
    jQuery('.already_student').show('fast');
}
function show_mentor_reg(){
    jQuery('.select_register_type').hide('fast');
    jQuery('#register_mentor').show('fast');
    jQuery('.already_student').show('fast');
}
function show_student_reg(){
    jQuery('.select_register_type').hide('fast');
    jQuery('#register_student').show('fast');
}
function show_login_form(){
    jQuery('.select_register_type').hide('fast');
    jQuery('#register_mentor').hide('fast');
    jQuery('#register_student').hide('fast');
    jQuery('.already_student').hide('fast');

    jQuery('#login_form').show('fast');
    jQuery(".new_user_question").show('fast');
}

function try_register_mentor(){
    console.log("lets try to register me!");
    jQuery(document).ready(function(e){
        jQuery.ajax({
           type: 'POST',
           url: ajax_object.ajax_url,
           data: { 
                'action': 'try_to_register', //calls wp_ajax_nopriv_ajaxremove
                'name': a,
                'email':e,
                'country':c,
                'organizaion':o,
                'username': document.getElementById('log_username').value,
                'password':document.getElementById('log_password').value
            },
           success: function(data){
                console.log(data);
               // window.location.href = data;
           },
           error: function(data){
               console.log(data);
           }
        });
    });
    return false;
}

function updateScroll(){
    var element = document.getElementById("messages");
    element.scrollTop = element.scrollHeight;
}

function trimfield(str){
    return str.replace(/^\s+|\s+$/g,'');
}

jQuery(document).ready(function(e) {
    jQuery('.settings_btn').click(function () {
        jQuery.ajax({
            type: 'POST',
            url: ajax_object.ajax_url,
            data: {
                'action': 'update_user_settings', //calls wp_ajax_nopriv_ajaxremove
                'line_of_work' : document.getElementById('up_line_of_work').value,
                'organization' : document.getElementById('up_organization').value,
                'qualification' : document.getElementById('up_qualification').value,
                'country' : document.getElementById('up_country').value,
                'MCsecurity':document.getElementById('update_settings_security').value
            },
            success: function(response) {
                //
                console.log("success");
                if (response.success === false) {
                    jQuery('#up_status').text(response.data.message);
                    jQuery('#up_status').show('slow');
                } else if (response.success === true) {
                    //window.location.href = response.data.message;
                    alert(response.data.message);
                }

            }
        });
    });
});
