jQuery(document).ready(function($) {
console.log("Loaded");
    jQuery('#assessement-example').click(function(){

            let data = {
                'action': 'assessement_check',
                'question-1' : jQuery('input[name=question-1]:checked', '#self-assessement-form').val(),
                'question-2' : jQuery('input[name=question-2]:checked', '#self-assessement-form').val(),
                'question-3' : jQuery('input[name=question-3]:checked', '#self-assessement-form').val(),
            };
            $.post('/wp-admin/admin-ajax.php', data, function(response) {
                jQuery('.assessement_precentage').text(response+'%');
            });
    });


});