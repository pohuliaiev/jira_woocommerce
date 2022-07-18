jQuery( ".more-button span" ).click(function() {
    jQuery('.loading').removeClass('d-none');
    var myModal = new bootstrap.Modal(document.getElementById('userTimeModal'), {
        keyboard: false
    });
    var user = jQuery(this).data('user');
    jQuery.ajax({
        type: "POST",
        url: ajaxurl,
        data : {
            action : 'jira_billable_rounded_admin_frontend',
            user : user
        },
        success: function (data) {
            jQuery('.modal-backdrop').remove();
            myModal.toggle();
            jQuery('#userTimeModal').html(data);
            jQuery('.loading').addClass('d-none');


        }
    });
});
/*
jQuery(document).on("click", ".pagination-load", function(){
    jQuery(this).remove();
    var spinner = '<div class="spinner-div"><div class="lds-ring"><div></div><div></div><div></div><div></div></div></div>';
    jQuery('.table').after(spinner);
    var page = jQuery(this).data( "page" );
    var project = jQuery('.issues-list').data( "project" );
    var start = jQuery('.issues-list').data( "start" );
    var total = jQuery('.issues-list').data( "total" );
    var user = jQuery('.issues-list').data( "user" );
    var next = page + 1;
    jQuery.ajax({
        type: "POST",
        url: ajaxurl,
        data : {
            action : 'jira_issues_pagination',
            page : page,
            user : user
        },
        success: function (data) {
            jQuery('.spinner-div').remove();
            jQuery('.issues-list').append(data);
            if(page < total){
                jQuery('.table').after('<div class="more-button pagination-load" data-page="' +next+ '" data-total="' +total+ '"><span>Show more</span></div>');
            }
        }
    });
});
    */