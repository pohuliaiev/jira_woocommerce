jQuery("#cron_checkbox").click(function () {
    if (jQuery(this).is(":checked")) {
        jQuery("#cron-interval").removeAttr("disabled");
        jQuery("#cron-interval").focus();
    } else {
        jQuery("#cron-interval").attr("disabled", "disabled");
    }
});


jQuery(document).on("click", ".ajax-call", function(){

    jQuery('.loading').removeClass('display-none');
    var myModal = new bootstrap.Modal(document.getElementById('userModal'), {
        keyboard: false
    });
    var user_id = jQuery(this).data( "user-id" );
    jQuery.ajax({
        type: "POST",
        url: ajaxurl,
        data : {
            action : 'jira_user_popup',
            user_id : user_id
        },
        success: function (data) {
            jQuery('#userModal').html(data);
            jQuery('.loading').addClass('display-none');
            myModal.toggle();

        }
    });
});

jQuery(document).on("click", ".update-user-time", function(){
    jQuery('.loading').removeClass('display-none');
    jQuery("#userModal").hide();
    jQuery('body').removeAttr("style");
    jQuery('body').removeClass('modal-open');
    jQuery('.modal-backdrop').remove();
    var user_id = jQuery(this).data( "user-id" );
    var booked_time = jQuery('#booked_plus').val();
    var message = jQuery('#message_time').val();
    var old_booked = jQuery('tr.'+user_id+' .booked').text();

    var newBooked = old_booked;
    if(booked_time){
        newBooked = parseInt(old_booked)+parseFloat(booked_time);
    }

    jQuery.ajax({
        type: "POST",
        url: ajaxurl,
        data : {
            action : 'jira_user_manual_update',
            user_id : user_id,
            booked_time : booked_time,
            old_booked_time : old_booked,
            message : message
        },
        success: function (data) {
            jQuery('tr.'+user_id+' .booked').html(parseFloat(newBooked));
            jQuery('.loading').addClass('display-none');


        }
    });
});

jQuery(document).on("click", ".bill", function(){
    jQuery('.loading').removeClass('display-none');
    var myModal = new bootstrap.Modal(document.getElementById('userTimeModal'), {
        keyboard: false
    });
    var user = jQuery(this).data('user');
    jQuery.ajax({
        type: "POST",
        url: ajaxurl,
        data : {
            action : 'jira_billable_admin',
            user : user
        },
        success: function (data) {
            jQuery('#userTimeModal').html(data);
            jQuery('.loading').addClass('display-none');
            myModal.toggle();

        }
    });
});

jQuery(document).on("click", ".billable-more", function(){
    var spinner = '<div class="spinner-div"><div class="lds-ring"><div></div><div></div><div></div><div></div></div></div>';
    jQuery(".billable-more").remove();
    jQuery('.modal-footer').append(spinner);
    var page = jQuery(this).data( "page" );
    var user = jQuery(this).data( "user" );
    var next = page + 1;
    var total = jQuery(this).data( "total" );
    jQuery.ajax({
        type: "POST",
        url: ajaxurl,
        data : {
            action : 'jira_billable_pagination_admin',
            page : page,
            user : user
        },
        success: function (data) {
            jQuery('.spinner-div').remove();
            jQuery('.billable-popup-table tbody').append(data);
            if(page < total){
                jQuery('.modal-footer').after('<div class="more-button billable-more" data-user="' +user+ '" data-page="' +next+ '" data-total="' +total+ '"><span>Show more</span></div>');
            }
        }
    });
});

jQuery(document).on("click", ".nonbill", function(){
    jQuery('.loading').removeClass('display-none');
    var myModal = new bootstrap.Modal(document.getElementById('userTimeModal'), {
        keyboard: false
    });
    var user = jQuery(this).data('user');
    jQuery.ajax({
        type: "POST",
        url: ajaxurl,
        data : {
            action : 'jira_nonbillable_admin',
            user : user
        },
        success: function (data) {
            jQuery('#userTimeModal').html(data);
            jQuery('.loading').addClass('display-none');
            myModal.toggle();

        }
    });
});

jQuery(document).on("click", ".nonbillable-more", function(){
    var spinner = '<div class="spinner-div"><div class="lds-ring"><div></div><div></div><div></div><div></div></div></div>';
    jQuery(".nonbillable-more").remove();
    jQuery('.modal-footer').append(spinner);
    var page = jQuery(this).data( "page" );
    var user = jQuery(this).data( "user" );
    var next = page + 1;
    var total = jQuery(this).data( "total" );
    jQuery.ajax({
        type: "POST",
        url: ajaxurl,
        data : {
            action : 'jira_nonbillable_pagination_admin',
            page : page,
            user : user
        },
        success: function (data) {
            jQuery('.spinner-div').remove();
            jQuery('.billable-popup-table tbody').append(data);
            if(page < total){
                jQuery('.modal-footer').after('<div class="more-button nonbillable-more" data-user="' +user+ '" data-page="' +next+ '" data-total="' +total+ '"><span>Show more</span></div>');
            }
        }
    });
});
jQuery(document).on("click", ".user-orders", function(){
    jQuery('.loading').removeClass('display-none');
    var myModal = new bootstrap.Modal(document.getElementById('userTimeModal'), {
        keyboard: false
    });
    var user = jQuery(this).data('user');
    jQuery.ajax({
        type: "POST",
        url: ajaxurl,
        data : {
            action : 'jira_user_orders_admin',
            user : user
        },
        success: function (data) {
            jQuery('#userTimeModal').html(data);
            jQuery('.loading').addClass('display-none');
            myModal.toggle();

        }
    });
});

jQuery(document).on("click", ".user-filters", function(){

    jQuery('.loading').removeClass('display-none');
    var myModal = new bootstrap.Modal(document.getElementById('userModal'), {
        keyboard: false
    });
    var user_id = jQuery(this).data( "user" );
    jQuery.ajax({
        type: "POST",
        url: ajaxurl,
        data : {
            action : 'jira_user_filter_popup',
            user_id : user_id
        },
        success: function (data) {
            jQuery('#userModal').html(data);
            jQuery('.loading').addClass('display-none');
            myModal.toggle();

        }
    });
});

jQuery(document).on("click", ".update-user-filter", function(){
    jQuery('.loading').removeClass('display-none');
    jQuery("#userModal").hide();
    jQuery('body').removeAttr("style");
    jQuery('body').removeClass('modal-open');
    jQuery('.modal-backdrop').remove();
    var user_id = jQuery(this).data( "user-id" );
    var filter_id = jQuery('#filter_id').val();

    jQuery.ajax({
        type: "POST",
        url: ajaxurl,
        data : {
            action : 'jira_user_filter_update',
            user_id : user_id,
            filter_id : filter_id
        },
        success: function (data) {
            jQuery('tr.'+user_id+' .filter_id').html(filter_id);
            jQuery('.loading').addClass('display-none');


        }
    });
});

jQuery(document).on("click", ".bill_rounded", function(){
    jQuery('.loading').removeClass('display-none');
    var myModal = new bootstrap.Modal(document.getElementById('userTimeModal'), {
        keyboard: false
    });
    var user = jQuery(this).data('user');
    jQuery.ajax({
        type: "POST",
        url: ajaxurl,
        data : {
            action : 'jira_billable_rounded_admin',
            user : user
        },
        success: function (data) {
            jQuery('#userTimeModal').html(data);
            jQuery('.loading').addClass('display-none');
            myModal.toggle();

        }
    });
});

jQuery(document).on("click", ".nonbill_rounded", function(){
    jQuery('.loading').removeClass('display-none');
    var myModal = new bootstrap.Modal(document.getElementById('userTimeModal'), {
        keyboard: false
    });
    var user = jQuery(this).data('user');
    jQuery.ajax({
        type: "POST",
        url: ajaxurl,
        data : {
            action : 'jira_nonbillable_rounded_admin',
            user : user
        },
        success: function (data) {
            jQuery('#userTimeModal').html(data);
            jQuery('.loading').addClass('display-none');
            myModal.toggle();

        }
    });
});


