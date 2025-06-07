jQuery("#company_id").change(function(){
    jQuery.get(main_url+"SubscriptionController/is_company_email/"+jQuery(this).val(), function(data, status){
        jQuery('#company_email').html(data);
    });
});