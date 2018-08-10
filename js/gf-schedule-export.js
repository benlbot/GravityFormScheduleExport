jQuery(function($){
    $('#activated').on('click', function() {
        if(this.checked){
            $('#gaddon-setting-row-'+this.name).show();
        } else {
            $('#gaddon-setting-row-'+this.name).hide();
        }
    });
    $('#export_period').on('change', function() {
        if(this.value == 'choose_period'){
            $('#gaddon-setting-row-custom_period').show();
        } else {
            $('#gaddon-setting-row-custom_period').hide();}
    });
    $('#export_frequency').on('change', function() {
        if(this.value == 'choose_frequence'){
            $('#gaddon-setting-row-custom_frequency').show();
        } else {
            $('#gaddon-setting-row-custom_frequency').hide();}
    });
    $('#export_email').on('click', function() {
        if(this.checked){
            $('#gaddon-setting-row-'+this.name).show();
            $('#gaddon-setting-row-mail_title').show();
            $('#gaddon-setting-row-mail_content').show();
        } else {
            $('#gaddon-setting-row-'+this.name).hide();
            $('#gaddon-setting-row-mail_title').hide();
            $('#gaddon-setting-row-mail_content').hide();
        }
    });
    $('#export_folder').on('click', function() {
        if(this.checked){
            $('#gaddon-setting-row-'+this.name).show();
        } else {
            $('#gaddon-setting-row-'+this.name).hide();
        }
    });
});