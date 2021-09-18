
// Perform AJAX login on form submit
jQuery(document).ready(function(e){
        jQuery('form#login_form p.status').show().text(ajax_login_object.loadingmessage);
       jQuery.ajax({
           type: 'POST',
           dataType: 'json',
           url: ajax_login_object.ajaxurl,
           data: { 
               'action': 'ajaxlogin', //calls wp_ajax_nopriv_ajaxlogin
               'username': document.getElementById('login_form').childNodes[3].childNodes[3].value,
               'password': document.getElementById('login_form').childNodes[5].childNodes[3].value,
               'security': jQuery('form#login_form #security').val() },
           success: function(data){
               jQuery('form#login_form p.status').text(data.message);
               if (data.loggedin == true){
                   document.location.href = ajax_login_object.redirecturl;
               }
           },
           error: function(data){
                console.log(data);
           }
       });
       return false;
});

// Perform AJAX login on form submit
jQuery(document).ready(function(e){
    jQuery('form#register_form_mentor p.status').show().text(ajax_login_object.loadingmessage);
        console.log('register mentor!');
    jQuery.ajax({
       type: 'POST',
       dataType: 'json',
       url: ajax_login_object.ajaxurl,
       data: { 
           'action': 'ajaxlogin', //calls wp_ajax_nopriv_ajaxlogin
           'username': document.getElementById('register_form_mentor').childNodes[3].childNodes[3].value,
           'password': document.getElementById('register_form_mentor').childNodes[5].childNodes[3].value,
           'security': jQuery('form#register_form_mentor #security').val() },
       success: function(data){
           jQuery('form#register_form_mentor p.status').text(data.message);
           if (data.loggedin == true){
               document.location.href = ajax_login_object.redirecturl;
           }
       },
       error: function(data){
            console.log(data);
       }
   });
   return false;
});

// Perform AJAX login on form submit
jQuery(document).ready(function(e){
    jQuery('form#register_form_student p.status').show().text(ajax_login_object.loadingmessage);
        console.log('register student!');
    jQuery.ajax({
       type: 'POST',
       dataType: 'json',
       url: ajax_login_object.ajaxurl,
       data: { 
           'action': 'ajaxlogin', //calls wp_ajax_nopriv_ajaxlogin
           'username': document.getElementById('register_form_student').childNodes[3].childNodes[3].value,
           'password': document.getElementById('register_form_student').childNodes[5].childNodes[3].value,
           'security': jQuery('form#register_form_student #security').val() },
       success: function(data){
           jQuery('form#register_form_student p.status').text(data.message);
           if (data.loggedin == true){
               document.location.href = ajax_login_object.redirecturl;
           }
       },
       error: function(data){
            console.log(data);
       }
   });
   return false;
});

