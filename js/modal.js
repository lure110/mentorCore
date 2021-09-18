jQuery(document).ready(function($) {
    jQuery('.modal-link').on('click', function () {
        let open = jQuery(this).attr("data-modal");
        let aditional = jQuery(this).attr("data-aditional");
        if(typeof aditional === "string")
        {
            console.log("HMM");
            jQuery('#modal-aditional-text', '.MC-modal[data-modal=' + open + ']').html(aditional).css('display', 'block');
        }
        jQuery('.MC-modal[data-modal=' + open + ']').css('display', 'block').addClass("active-modal");

    });

    jQuery('.modal-close').on('click', function () {
        jQuery('.MC-modal.active-modal').css('display', '').removeClass('active-modal');
    });
    jQuery('.MC-modal').on('click', function () {
        jQuery('.MC-modal.active-modal').css('display', '').removeClass('active-modal');
    });
    jQuery('.modal-content').on('click', function (e) {
        e.stopPropagation();
    });
});



